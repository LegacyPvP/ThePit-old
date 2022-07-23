<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\KnockBackManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Objects\Language;
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
    private PlayerProperties $properties;
    private CompoundTag $tag;
    private bool $teleportation = false;
    public string $targetName = "";



    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->tag = $nbt;
        $this->properties = new PlayerProperties($this);
    }

    public function getNBT(): CompoundTag{
        return $this->tag;
    }

    public function setNBT(CompoundTag $nbt): void{
        $this->tag = $nbt;
    }

    public function getPlayerProperties(): PlayerProperties{
        return $this->properties;
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
        return LanguageManager::parseLanguage(parent::getLocale());
    }

    public function syncNBT(): void
    {
        $this->setNBT($this->saveNBT());
    }

    public function onUpdate(int $currentTick): bool
    {//oui ?
        $this->setNameTag($this->getRank()->getNametag($this) . TextFormat::GOLD . str_replace("{prime}", $this->getPlayerProperties()->getNestedProperties("stats.prime"), $this->getRank()->getNametag($this)) . "\n" . str_replace("{level}", $this->getPlayerProperties()->getNestedProperties("stats.level"), $this->getPlayerProperties()->getNestedProperties("stats.prestige")));
        $this->setScoreTag($this->getRank()->getScoretag($this));
        $currentTick % 20 !== 0 ?: $this->syncNBT();

        return parent::onUpdate($currentTick);
    }

    private function updateNightVision(): void {
        match(true){
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

    public function getTranslation(string $message, array $parameters = []): string {
        $parameters = array_map(fn(string|Translatable $p) => $p instanceof Translatable ? $this->getLanguage()->translate($p) : $p, $parameters);
        if(!$this->server->isLanguageForced()){
            foreach($parameters as $i => $p){
                $parameters[$i] = $this->getLanguage()->translateString($p, [], "pocketmine.");
            }
            return TextPacket::translation($this->getLanguage()->translateString($message, $parameters, "pocketmine."), $parameters)->message;
        }
        return $this->getLanguage()->translateString($message, $parameters);
    }

    public function sendTranslation(string $message, array $parameters = []): void
    {
        $parameters = array_map(fn(string|Translatable $p) => $p instanceof Translatable ? $this->getLanguage()->translate($p) : $p, $parameters);
        if(!$this->server->isLanguageForced()){
            foreach($parameters as $i => $p){
                $parameters[$i] = $this->getLanguage()->translateString($p, [], "pocketmine.");
            }
            $this->getNetworkSession()->onTranslatedChatMessage($this->getLanguage()->translateString($message, $parameters, "pocketmine."), $parameters);
        }else{
            $this->sendMessage($this->getLanguage()->translateString($message, $parameters));
        }
    }

    public function attack(EntityDamageEvent $source): void
    {
        if(!$this->isAlive()){
            return;
        }

        if($this->isCreative()
            && $source->getCause() !== EntityDamageEvent::CAUSE_SUICIDE
        ){
            $source->cancel();
        }elseif($this->allowFlight && $source->getCause() === EntityDamageEvent::CAUSE_FALL){
            $source->cancel();
        }

        if($this->noDamageTicks > 0){
            $source->cancel();
        }

        if($this->effectManager->has(VanillaEffects::FIRE_RESISTANCE()) && (
                $source->getCause() === EntityDamageEvent::CAUSE_FIRE
                || $source->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK
                || $source->getCause() === EntityDamageEvent::CAUSE_LAVA
            )
        ){
            $source->cancel();
        }

        $this->applyDamageModifiers($source);

        if($source instanceof EntityDamageByEntityEvent && (
                $source->getCause() === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION ||
                $source->getCause() === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION)
        ){
            //TODO: knockback should not just apply for entity damage sources
            //this doesn't matter for TNT right now because the PrimedTNT entity is considered the source, not the block.
            $base = $source->getKnockBack();
            $source->setKnockBack($base - min($base, $base * $this->getHighestArmorEnchantmentLevel(VanillaEnchantments::BLAST_PROTECTION()) * 0.15));
        }

        $source->call();
        if($source->isCancelled()){
            return;
        }

        $this->setLastDamageCause($source);

        $this->setHealth($this->getHealth() - $source->getFinalDamage());

        $this->attackTime = $source->getAttackCooldown();

        if($source instanceof EntityDamageByChildEntityEvent){
            $e = $source->getChild();
            if($e !== null){
                $motion = $e->getMotion();
                $this->knockBack($motion->x, $motion->z, KnockBackManager::getHorizontal(), KnockBackManager::getVertical());
            }
        }elseif($source instanceof EntityDamageByEntityEvent){
            $e = $source->getDamager();
            if($e !== null){
                $deltaX = $this->location->x - $e->location->x;
                $deltaZ = $this->location->z - $e->location->z;
                $this->knockBack($deltaX, $deltaZ, KnockBackManager::getHorizontal(), KnockBackManager::getVertical());
            }
        }

        if($this->isAlive()){
            $this->applyPostDamageEffects($source);
            $this->doHitAnimation();
        }
    }

    public function isInTeleportation(): bool
    {
        return $this->teleportation;
    }

    public function setTeleportation(bool $teleportation): void
    {
        $this->teleportation = $teleportation;
    }

    public function getGold(): int
    {
        return $this->getPlayerProperties()->getNestedProperties('money.gold');
    }

    public function setGold(int $gold): void
    {
        $this->getPlayerProperties()->setNestedProperties('money.gold', $gold);
    }

    public function addGold(int $amount): void
    {
        $this->setGold($this->getGold() + $amount);
    }

    public function removeGold(int $amount): void
    {
        $this->setGold($this->getGold() - $amount);
    }

    public function hasGold(int $amount): bool
    {
        return $this->getGold() >= $amount;
    }

    public function getStars(): int
    {
        return $this->getPlayerProperties()->getNestedProperties('money.etoiles');
    }

    public function setStars(int $gold): void
    {
        $this->getPlayerProperties()->setNestedProperties('money.etoiles', $gold);
    }

    public function addStars(int $amount): void
    {
        $this->setStars($this->getStars() + $amount);
    }

    public function removeStars(int $amount): void
    {
        $this->setStars($this->getStars() - $amount);
    }

    public function hasStars(int $amount): bool
    {
        return $this->getStars() >= $amount;
    }

    public function getVoteCoins(): int
    {
        return $this->getPlayerProperties()->getNestedProperties('money.votecoins');
    }

    public function setVoteCoins(int $gold): void
    {
        $this->getPlayerProperties()->setNestedProperties('money.votecoins', $gold);
    }

    public function addVoteCoins(int $amount): void
    {
        $this->setVoteCoins($this->getVoteCoins() + $amount);
    }

    public function removeVoteCoins(int $amount): void
    {
        $this->setVoteCoins($this->getVoteCoins() - $amount);
    }

    public function hasVoteCoins(int $amount): bool
    {
        return $this->getVoteCoins() >= $amount;
    }

    public function getCredits(): int
    {
        return $this->getPlayerProperties()->getNestedProperties('money.credits');
    }

    public function setCredits(int $gold): void
    {
        $this->getPlayerProperties()->setNestedProperties('money.credits', $gold);
    }

    public function addCredits(int $amount): void
    {
        $this->setCredits($this->getCredits() + $amount);
    }

    public function removeCredits(int $amount): void
    {
        $this->setCredits($this->getCredits() - $amount);
    }

    public function hasCredits(int $amount): bool
    {
        return $this->getCredits() >= $amount;
    }

    public function getRank(): Rank
    {
        return RanksManager::parseRank($this->getPlayerProperties()->getNestedProperties('infos.rank'));
    }

    public function isInCombat()
    {
        return $this->getPlayerProperties()->getNestedProperties('status.combat');
    }

    public function setInCombat(bool $combat, LegacyPlayer $target): void
    {
        if($combat == true){
            $this->targetName = $target->getName();
            $this->getPlayerProperties()->setNestedProperties('status.combat_players', [$this->getName(), $target->getName()]);
            $this->getPlayerProperties()->setNestedProperties('status.combat', true);
        }else{
            $this->getPlayerProperties()->setNestedProperties('status.combat_players', []);
            $this->getPlayerProperties()->setNestedProperties('status.combat', false);
        }
    }

    public function dropInventory()
    {
        foreach($this->getInventory()->getContents() as $item){
            $this->dropItem($item);
        }
    }
}