<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

namespace Legacy\ThePit\player;

use Legacy\ThePit\entities\list\FishingHook;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\objects\Message;
use Legacy\ThePit\providers\CurrencyProvider;
use Legacy\ThePit\objects\Rank;
use Legacy\ThePit\objects\Language;
use Legacy\ThePit\providers\PerksProvider;
use Legacy\ThePit\utils\EquipmentUtils;
use Legacy\ThePit\traits\CacheTrait;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\EffectManager;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\ExperienceManager;
use pocketmine\entity\HungerManager;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemFactory;
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
    private PerksProvider $perksProvider;
    private CompoundTag $tag;
    public string $targetName = "";
    private ?FishingHook $isFishing = null;

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->tag = $nbt;
        $this->properties = new PlayerProperties($this);
        $this->currencyProvider = new CurrencyProvider($this);
        $this->perksProvider = new PerksProvider($this);
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

    public function getPerksProvider(): PerksProvider
    {
        return $this->perksProvider;
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
        if($this->getPlayerProperties()->getNestedProperties("stats.prime") >= 0){
            $this->setNameTag($this->getRank()->getNametag($this) . TextFormat::GOLD . "\n" . str_replace("{level}", $this->getPlayerProperties()->getNestedProperties("stats.level"), $this->getPlayerProperties()->getNestedProperties("stats.prestige")));
        }else{
            $prime = $this->getPlayerProperties()->getNestedProperties("stats.prime");
            $this->setNameTag("§l§e$prime GOLD" . "\n" . $this->getRank()->getNametag($this) . TextFormat::GOLD . "\n" . str_replace("{level}", $this->getPlayerProperties()->getNestedProperties("stats.level"), $this->getPlayerProperties()->getNestedProperties("stats.prestige")));
        }
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
            $this->getPerksProvider()->onEvent($source);
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
        $factory = ItemFactory::getInstance();
        $this->getInventory()->clearAll();
        $this->getArmorInventory()->clearAll();

        $helmet = $factory->get(EquipmentUtils::getArmorId(EquipmentUtils::HELMET, $this->getArmorLevel(EquipmentUtils::HELMET)));
        $chestplate = $factory->get(EquipmentUtils::getArmorId(EquipmentUtils::CHESTPLATE, $this->getArmorLevel(EquipmentUtils::CHESTPLATE)));
        $leggings = $factory->get(EquipmentUtils::getArmorId(EquipmentUtils::LEGGINGS, $this->getArmorLevel(EquipmentUtils::LEGGINGS)));
        $boots = $factory->get(EquipmentUtils::getArmorId(EquipmentUtils::BOOTS, $this->getArmorLevel(EquipmentUtils::BOOTS)));

        $this->getArmorInventory()->setHelmet($helmet);
        $this->getArmorInventory()->setChestplate($chestplate);
        $this->getArmorInventory()->setLeggings($leggings);
        $this->getArmorInventory()->setBoots($boots);

        $arrow_ = EquipmentUtils::getWeaponId(EquipmentUtils::ARROW, $this->getWeaponLevel(EquipmentUtils::ARROW));

        $sword = $factory->get(EquipmentUtils::getWeaponId(EquipmentUtils::SWORD, $this->getWeaponLevel(EquipmentUtils::SWORD)));
        $bow = $factory->get(EquipmentUtils::getWeaponId(EquipmentUtils::BOW, $this->getWeaponLevel(EquipmentUtils::BOW)));
        $arrow = $factory->get($arrow_[0], $arrow_[1], $arrow_[2]);

        $this->getInventory()->setItem(0, $sword);
        $this->getInventory()->setItem(1, $bow);
        $this->getInventory()->setItem(8, $arrow);

        $snowball_ = EquipmentUtils::getWeaponId(EquipmentUtils::SNOWBALL, $this->getSupportLevel(EquipmentUtils::SNOWBALL));
        $block_ = EquipmentUtils::getWeaponId(EquipmentUtils::BLOCKS, $this->getSupportLevel(EquipmentUtils::BLOCKS));

        $hook = $factory->get(EquipmentUtils::getSupportId(EquipmentUtils::HOOK, $this->getSupportLevel(EquipmentUtils::HOOK)));
        $bucket_lava = $factory->get(EquipmentUtils::getSupportId(EquipmentUtils::BUCKET_LAVA, $this->getSupportLevel(EquipmentUtils::BUCKET_LAVA)));
        $flap = $factory->get(EquipmentUtils::getSupportId(EquipmentUtils::FLAP, $this->getSupportLevel(EquipmentUtils::FLAP)));
        $nemo = $factory->get(EquipmentUtils::getSupportId(EquipmentUtils::NEMO, $this->getSupportLevel(EquipmentUtils::NEMO)));
        $snowball = $factory->get($snowball_[0], $snowball_[1], $snowball_[2]);
        $block = $factory->get($block_[0], $block_[1], $block_[2]);

        if ($this->getSupportLevel(EquipmentUtils::HOOK) != 0 and $this->getInventory()->canAddItem($hook)) {
            $this->getInventory()->addItem($hook);
        }

        if ($this->getSupportLevel(EquipmentUtils::BUCKET_LAVA) != 0 and $this->getInventory()->canAddItem($bucket_lava)) {
            $this->getInventory()->addItem($bucket_lava);
        }
        
        if ($this->getSupportLevel(EquipmentUtils::SNOWBALL) != 0 and $this->getInventory()->canAddItem($snowball)) {
          $this->getInventory()->addItem($snowball);
        }
        
        if ($this->getSupportLevel(EquipmentUtils::BLOCKS) != 0 and $this->getInventory()->canAddItem($block)){
          $this->addItem($block);
        }
        
        if ($this->getSupportLevel(EquipmentUtils::FLAP) != 0 and $this->getInventory()->canAddItem($flap)){
          $this->addItem($flap);
        }
        
        if ($this->getSupportLevel(EquipmentUtils::NEMO) != 0 and $this->getInventory()->canAddItem($nemo)){
          $this->addItem($nemo);
        }
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
        return match ($index) {
            EquipmentUtils::HELMET => $this->getLanguage()->getMessage("equipment.helmet", [], false),
            EquipmentUtils::CHESTPLATE => $this->getLanguage()->getMessage("equipment.chestplate", [], false),
            EquipmentUtils::LEGGINGS => $this->getLanguage()->getMessage("equipment.leggings", [], false),
            EquipmentUtils::BOOTS => $this->getLanguage()->getMessage("equipment.boots", [], false),
            default => null,
        };
    }

    public function upgradeArmor(int $index){
        return match ($index) {
            EquipmentUtils::HELMET => $this->getPlayerProperties()->setNestedProperties("inventory.helmet", $this->getPlayerProperties()->getNestedProperties("inventory.helmet") + 1),
            EquipmentUtils::CHESTPLATE => $this->getPlayerProperties()->setNestedProperties("inventory.chestplate", $this->getPlayerProperties()->getNestedProperties("inventory.chestplate") + 1),
            EquipmentUtils::LEGGINGS => $this->getPlayerProperties()->setNestedProperties("inventory.leggings", $this->getPlayerProperties()->getNestedProperties("inventory.leggings") + 1),
            EquipmentUtils::BOOTS => $this->getPlayerProperties()->setNestedProperties("inventory.boots", $this->getPlayerProperties()->getNestedProperties("inventory.boots") + 1),
            default => null,
        };
    }

    public function getWeaponLevel(int $index)
    {
        return match ($index) {
            EquipmentUtils::SWORD => $this->getPlayerProperties()->getNestedProperties("inventory.sword"),
            EquipmentUtils::BOW => $this->getPlayerProperties()->getNestedProperties("inventory.bow"),
            EquipmentUtils::ARROW => $this->getPlayerProperties()->getNestedProperties("inventory.arrow"),
            default => null,
        };
    }

    public function getWeapon(int $index): ? Message{
        return match ($index) {
            EquipmentUtils::SWORD => $this->getLanguage()->getMessage("equipment.sword", [], false),
            EquipmentUtils::BOW => $this->getLanguage()->getMessage("equipment.bow", [], false),
            EquipmentUtils::ARROW => $this->getLanguage()->getMessage("equipment.arrow", [], false),
            default => null,
        };
    }

    public function upgradeWeapon(int $index){
        return match ($index) {
            EquipmentUtils::SWORD => $this->getPlayerProperties()->setNestedProperties("inventory.sword", $this->getPlayerProperties()->getNestedProperties("inventory.sword") + 1),
            EquipmentUtils::BOW => $this->getPlayerProperties()->setNestedProperties("inventory.bow", $this->getPlayerProperties()->getNestedProperties("inventory.bow") + 1),
            EquipmentUtils::ARROW => $this->getPlayerProperties()->setNestedProperties("inventory.arrow", $this->getPlayerProperties()->getNestedProperties("inventory.arrow") + 1),
            default => null,
        };
    }

    public function getSupportLevel(int $index)
    {
        return match ($index) {
            EquipmentUtils::HOOK => $this->getPlayerProperties()->getNestedProperties("inventory.hook"),
            EquipmentUtils::BUCKET_LAVA => $this->getPlayerProperties()->getNestedProperties("inventory.bucket_lava"),
            EquipmentUtils::SNOWBALL => $this->getPlayerProperties()->getNestedProperties("inventory.snowball"),
            EquipmentUtils::BLOCKS => $this->getPlayerProperties()->getNestedProperties("inventory.block"),
            EquipmentUtils::FLAP => $this->getPlayerProperties()->getNestedProperties("inventory.flap"),
            EquipmentUtils::NEMO => $this->getPlayerProperties()->getNestedProperties("inventory.nemo"),
            default => null,
        };
    }

    public function getSupport(int $index): ? Message{
        return match ($index) {
            EquipmentUtils::HOOK => $this->getLanguage()->getMessage("equipment.hook", [], false),
            EquipmentUtils::BUCKET_LAVA => $this->getLanguage()->getMessage("equipment.bucket_lava", [], false),
            EquipmentUtils::SNOWBALL => $this->getLanguage()->getMessage("equipment.snowball", [], false),
            EquipmentUtils::BLOCKS => $this->getLanguage()->getMessage("equipment.block", [], false),
            EquipmentUtils::FLAP => $this->getLanguage()->getMessage("equipment.flap", [], false),
            EquipmentUtils::NEMO => $this->getLanguage()->getMessage("equipment.nemo", [], false),
            default => null,
        };
    }

    public function upgradeSupport(int $index){
        return match ($index) {
            EquipmentUtils::HOOK => $this->getPlayerProperties()->setNestedProperties("inventory.hook", $this->getPlayerProperties()->getNestedProperties("inventory.hook") + 1),
            EquipmentUtils::BUCKET_LAVA => $this->getPlayerProperties()->setNestedProperties("inventory.bucket_lava", $this->getPlayerProperties()->getNestedProperties("inventory.bucket_lava") + 1),
            EquipmentUtils::SNOWBALL => $this->getPlayerProperties()->setNestedProperties("inventory.snowball", $this->getPlayerProperties()->getNestedProperties("inventory.snowball") + 1),
            EquipmentUtils::BLOCKS => $this->getPlayerProperties()->setNestedProperties("inventory.block", $this->getPlayerProperties()->getNestedProperties("inventory.block") + 1),
            EquipmentUtils::FLAP => $this->getPlayerProperties()->setNestedProperties("inventory.flap", $this->getPlayerProperties()->getNestedProperties("inventory.flap") + 1),
            EquipmentUtils::NEMO => $this->getPlayerProperties()->setNestedProperties("inventory.nemo", $this->getPlayerProperties()->getNestedProperties("inventory.nemo") + 1),
            default => null,
        };
    }
}