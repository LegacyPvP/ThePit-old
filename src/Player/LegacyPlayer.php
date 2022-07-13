<?php
namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\KnockBackManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Objects\Language;
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

final class LegacyPlayer extends Player
{
    private PlayerProperties $properties;
    private CompoundTag $tag;
    private bool $nightvision = false;
    private bool $teleportation = false;

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

    public function getGrade(): Rank
    {
        return RanksManager::parseRank($this->getPlayerProperties()->getNestedProperties("infos.rank")) ?? RanksManager::getDefaultRank();
    }

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTag($this->getGrade()->getNametag($this));
        $this->setScoreTag($this->getGrade()->getScoretag($this));
        $currentTick % 20 !== 0 ?: $this->syncNBT();
        return parent::onUpdate($currentTick);
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

    public function isInNightvision(): bool
    {
        return $this->nightvision;
    }

    public function setNightvision(bool $nightvision): void
    {
        $this->nightvision = $nightvision;
    }

    public function isInTeleportation(): bool
    {
        return $this->teleportation;
    }

    public function setTeleportation(bool $teleportation): void
    {
        $this->teleportation = $teleportation;
    }
}