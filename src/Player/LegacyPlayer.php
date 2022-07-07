<?php
namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Objects\Language;
use Legacy\ThePit\Utils\PlayerUtils;
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
        $nbt = parent::saveNBT();
        foreach ($this->properties->getPropertiesList() as $property => $value){
            $nbt = PlayerUtils::valueToTag($property, $value, $nbt);
        }
        return $nbt;
    }

    public function getLanguage(): Language
    {
        return LanguageManager::parseLanguage(parent::getLocale());
    }

}