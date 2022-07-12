<?php
namespace Legacy\ThePit\Player;

use Legacy\ThePit\Core;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Objects\Language;
use Legacy\ThePit\Utils\PlayerUtils;
use pocketmine\entity\effect\EffectManager;
use pocketmine\entity\ExperienceManager;
use pocketmine\entity\HungerManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\KnownTranslationKeys;
use pocketmine\lang\Translatable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;

final class LegacyPlayer extends Player
{
    private PlayerProperties $properties;
    private CompoundTag $tag;

    public function initEntity(CompoundTag $nbt): void
    {
        $this->tag = $nbt;
        $this->properties = new PlayerProperties($this);
        parent::initEntity($nbt);
    }

    public function getNBT(): CompoundTag{
        return $this->tag;
    }

    public function getPlayerProperties(): PlayerProperties{
        return $this->properties;
    }

    public function saveNBT(): CompoundTag
    {
        $this->effectManager ??= new EffectManager($this);
        $this->hungerManager ??= new HungerManager($this);
        $this->xpManager ??= new ExperienceManager($this);
        if($this->spawned){
            $nbt = parent::saveNBT();
            foreach ($this->properties->getPropertiesList() as $property => $value){
                $nbt = PlayerUtils::valueToTag($property, $value, $nbt);
            }
            return $nbt;
        }
        return new CompoundTag();
    }

    public function getLanguage(): Language
    {
        return LanguageManager::parseLanguage(parent::getLocale());
    }

    public function getGrade(): Rank
    {
        return RanksManager::parseRank($this->getPlayerProperties()->getNestedProperties("infos.rank")) ?? RanksManager::getDefaultRank();
    }

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTag($this->getGrade()->getNametag($this));
        $this->setScoreTag($this->getGrade()->getScoretag($this));
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

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        parent::knockBack(Core::getInstance()->getConfigByName("knockback")->get("x"), Core::getInstance()->getConfigByName("knockback")->get("y"), Core::getInstance()->getConfigByName("knockback")->get("force"), Core::getInstance()->getConfigByName("knockback")->get("vertical-limit"));
    }

    public function attack(EntityDamageEvent $source): void
    {
        $source->setAttackCooldown(Core::getInstance()->getConfigByName("knockback")->get("attack-cooldown"));
    }
}