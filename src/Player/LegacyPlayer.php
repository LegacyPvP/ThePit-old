<?php
namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Objects\Language;
use Legacy\ThePit\Utils\PlayerUtils;
use pocketmine\entity\effect\EffectManager;
use pocketmine\entity\ExperienceManager;
use pocketmine\entity\HungerManager;
use pocketmine\nbt\tag\CompoundTag;
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
        $this->saveNBT();
        return parent::onUpdate($currentTick);
    }
}