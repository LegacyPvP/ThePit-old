<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Entities\List\FishingHook;
use Legacy\ThePit\Managers\Managers;
use Legacy\ThePit\Objects\Message;
use Legacy\ThePit\Providers\CurrencyProvider;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Objects\Language;
use Legacy\ThePit\Utils\EquipmentUtils;
use Legacy\ThePit\Traits\CacheTrait;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\EffectManager;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\ExperienceManager;
use pocketmine\entity\HungerManager;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\lang\Translatable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class LegacyPlayer extends Player
{
    use CacheTrait;

    private PlayerProperties $properties;
    private CurrencyProvider $currencyProvider;
    private CompoundTag $tag;
    public string $targetName = "";
    private ?FishingHook $isFishing = null;

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->tag = $nbt;
        $this->properties = new PlayerProperties($this);
        $this->currencyProvider = new CurrencyProvider($this);
    }

    public function getNBT(): CompoundTag
    {
        return $this->tag;
    }

    public function setNBT(CompoundTag $nbt): void
    {
        $this->tag = $nbt;
    }

    public function getPlayerProperties(): PlayerProperties
    {
        return $this->properties;
    }

    public function getCurrencyProvider(): CurrencyProvider
    {
        return $this->currencyProvider;
    }

    //TODO: The problem with a player NBT should not crash the server.
    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $this->effectManager ??= new EffectManager($this);
        $this->hungerManager ??= new HungerManager($this);
        $this->xpManager ??= new ExperienceManager($this);
        $this->updateNightVision();
        !isset($this->properties) ?: $this->properties->save($nbt);
        return $nbt;
    }

    public function getLanguage(): Language
    {
        return Managers::LANGUAGES()->get(parent::getLocale());
    }

    public function syncNBT(): void
    {
        $this->setNBT($this->saveNBT());
    }

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTag($this->getRank()->getNametag($this) . TextFormat::GOLD . "\n" . str_replace("{level}", $this->getPlayerProperties()->getNestedProperties("stats.level"), $this->getPlayerProperties()->getNestedProperties("stats.prestige")));
        $this->setScoreTag($this->getRank()->getScoretag($this));
        $currentTick % 20 !== 0 ?: $this->syncNBT();

        return parent::onUpdate($currentTick);
    }

    private function updateNightVision(): void
    {
        match (true) {
            $this->getPlayerProperties()->getNestedProperties('status.nightvision') and !$this->getEffects()->has(VanillaEffects::NIGHT_VISION()) => $this->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 999999, 255, false)),
            !$this->getPlayerProperties()->getNestedProperties('status.nightvision') and $this->getEffects()->has(VanillaEffects::NIGHT_VISION()) => $this->getEffects()->remove(VanillaEffects::NIGHT_VISION()),
            default => null,
        };
    }

    public function setGamemode(GameMode $gm): bool
    {
        $this->getLanguage()->getMessage("messages.commands.gamemode.change", ["{gamemode}" => $gm->getEnglishName()])->send($this);
        return parent::setGamemode($gm);
    }

    public function getTranslation(string $message, array $parameters = []): string
    {
        $parameters = array_map(fn(string|Translatable $p) => $p instanceof Translatable ? $this->getLanguage()->translate($p) : $p, $parameters);
        if (!$this->server->isLanguageForced()) {
            foreach ($parameters as $i => $p) {
                $parameters[$i] = $this->getLanguage()->translateString($p, [], "pocketmine.");
            }
            return TextPacket::translation($this->getLanguage()->translateString($message, $parameters, "pocketmine."), $parameters)->message;
        }
        return $this->getLanguage()->translateString($message, $parameters);
    }

    public function sendTranslation(string $message, array $parameters = []): void
    {
        $parameters = array_map(fn(string|Translatable $p) => $p instanceof Translatable ? $this->getLanguage()->translate($p) : $p, $parameters);
        if (!$this->server->isLanguageForced()) {
            foreach ($parameters as $i => $p) {
                $parameters[$i] = $this->getLanguage()->translateString($p, [], "pocketmine.");
            }
            $this->getNetworkSession()->onTranslatedChatMessage($this->getLanguage()->translateString($message, $parameters, "pocketmine."), $parameters);
        } else {
            $this->sendMessage($this->getLanguage()->translateString($message, $parameters));
        }
    }

    public function attack(EntityDamageEvent $source): void
    {
        if (!$this->isAlive()) {
            return;
        }

        if ($this->isCreative()
            && $source->getCause() !== EntityDamageEvent::CAUSE_SUICIDE
        ) {
            $source->cancel();
        } elseif ($this->allowFlight && $source->getCause() === EntityDamageEvent::CAUSE_FALL) {
            $source->cancel();
        }

        if ($this->noDamageTicks > 0) {
            $source->cancel();
        }

        if ($this->effectManager->has(VanillaEffects::FIRE_RESISTANCE()) && (
                $source->getCause() === EntityDamageEvent::CAUSE_FIRE
                || $source->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK
                || $source->getCause() === EntityDamageEvent::CAUSE_LAVA
            )
        ) {
            $source->cancel();
        }

        $this->applyDamageModifiers($source);

        if ($source instanceof EntityDamageByEntityEvent && (
                $source->getCause() === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION ||
                $source->getCause() === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION)
        ) {
            //TODO: knockback should not just apply for entity damage sources
            //this doesn't matter for TNT right now because the PrimedTNT entity is considered the source, not the block.
            $base = $source->getKnockBack();
            $source->setKnockBack($base - min($base, $base * $this->getHighestArmorEnchantmentLevel(VanillaEnchantments::BLAST_PROTECTION()) * 0.15));
        }

        $source->call();
        if ($source->isCancelled()) {
            return;
        }

        $this->setLastDamageCause($source);

        $this->setHealth($this->getHealth() - $source->getFinalDamage());

        $this->attackTime = $source->getAttackCooldown();

        if ($source instanceof EntityDamageByChildEntityEvent) {
            $e = $source->getChild();
            if ($e !== null) {
                $motion = $e->getMotion();
                $this->knockBack($motion->x, $motion->z, Managers::KNOCKBACK()->getHorizontal(), Managers::KNOCKBACK()->getVertical());
            }
        } elseif ($source instanceof EntityDamageByEntityEvent) {
            $e = $source->getDamager();
            if ($e !== null) {
                $deltaX = $this->location->x - $e->location->x;
                $deltaZ = $this->location->z - $e->location->z;
                $this->knockBack($deltaX, $deltaZ, Managers::KNOCKBACK()->getHorizontal(), Managers::KNOCKBACK()->getVertical());
            }
        }

        if ($this->isAlive()) {
            $this->applyPostDamageEffects($source);
            $this->doHitAnimation();
        }
    }

    public function getRank(): Rank
    {
        return Managers::RANKS()->get($this->getPlayerProperties()->getNestedProperties('infos.rank'));
    }

    public function dropInventory()
    {
        foreach ($this->getInventory()->getContents() as $item) {
            $this->dropItem($item);
        }
    }

    public function setStuff(): void {
        $helmet = $this->getPlayerProperties()->getNestedProperties("inventory.helmet");
        $chestplate = $this->getPlayerProperties()->getNestedProperties("inventory.chestplate");
        $leggings = $this->getPlayerProperties()->getNestedProperties("inventory.leggings");
        var_dump($boots = $this->getPlayerProperties()->getNestedProperties("inventory.boots"));

        $this->getArmorInventory()->setHelmet($helmet);
        $this->getArmorInventory()->setChestplate($chestplate);
        $this->getArmorInventory()->setLeggings($leggings);
        $this->getArmorInventory()->setBoots($boots);

        $sword = $this->getPlayerProperties()->getNestedProperties("inventory.sword");
        $bow = $this->getPlayerProperties()->getNestedProperties("inventory.bow");
        $arrow = $this->getPlayerProperties()->getNestedProperties("inventory.arrow");

        $this->getInventory()->setItem(0, $sword);
        $this->getInventory()->setItem(1, $bow);
        $this->getInventory()->setItem(8, $arrow);
    }

    public function getFishingHook(): ?FishingHook
    {
        return $this->isFishing;
    }

    public function setFishing(?FishingHook $fishing): void
    {
        $this->isFishing = $fishing;
    }

    public function getArmorLevel(int $index)
    {
        return match ($index) {
            EquipmentUtils::HELMET => $this->getPlayerProperties()->getNestedProperties("inventory.helmet"),
            EquipmentUtils::CHESTPLATE => $this->getPlayerProperties()->getNestedProperties("inventory.chestplate"),
            EquipmentUtils::LEGGINGS => $this->getPlayerProperties()->getNestedProperties("inventory.leggings"),
            EquipmentUtils::BOOTS => $this->getPlayerProperties()->getNestedProperties("inventory.boots"),
            default => null,
        };
    }

    public function getArmor(int $index): ? Message
    {
        $level = $this->getArmorLevel($index);
        return match ($level) {
            EquipmentUtils::HELMET => $this->getLanguage()->getMessage("equipment.helmet"),
            EquipmentUtils::CHESTPLATE => $this->getLanguage()->getMessage("equipment.chestplate"),
            EquipmentUtils::LEGGINGS => $this->getLanguage()->getMessage("equipment.leggings"),
            EquipmentUtils::BOOTS => $this->getLanguage()->getMessage("equipment.boots"),
            default => null,
        };
    }

    public function getWeaponsLevel(int $index)
    {
        return match ($index) {
            EquipmentUtils::SWORD => $this->getPlayerProperties()->getNestedProperties("inventory.sword"),
            EquipmentUtils::BOW => $this->getPlayerProperties()->getNestedProperties("inventory.bow"),
            EquipmentUtils::ARROW => $this->getPlayerProperties()->getNestedProperties("inventory.arrow"),
            default => null,
        };
    }

    public function getWeapons(int $index): ? Message {
        $level = $this->getArmorLevel($index);
        return match ($index) {
            EquipmentUtils::SWORD => $this->getLanguage()->getMessage("equipment.sword"),
            EquipmentUtils::BOW => $this->getLanguage()->getMessage("equipment.bow"),
            EquipmentUtils::ARROW => $this->getLanguage()->getMessage("equipment.arrow"),
            default => null,
        };
    }
}