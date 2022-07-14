<?php

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Traits\PropertiesTrait;
use Legacy\ThePit\Utils\PlayerUtils;
use Legacy\ThePit\Utils\SpellUtils;
use pocketmine\nbt\tag\CompoundTag;

final class PlayerProperties {
    use PropertiesTrait;

    public function __construct(public LegacyPlayer $player)
    {
        if(!($nbt = $this->player->getNBT())->getCompoundTag('properties') || empty($nbt->getCompoundTag("properties")->getValue())){
            $this->setBaseProperties([
                "stats" => [
                    "kills" => 0,
                    "deaths" => 0,
                    "kdr" => 0.0,
                    "level" => 1,
                    "xp" => 0,
                    "or" => 0,
                    "credits" => 0,
                    "etoiles" => 0,
                    "killstreak" => 0,
                    "votecoins" => 0,
                    "prestige" => 0,
                    "keys" => 0,
                ],
                "infos" => [
                    "ip" => "",
                    "platform" => "test",
                    "rank" => RanksManager::getDefaultRank()?->getName(),
                ],
                "status" => [
                    "nightvision" => false,
                    "freezed" => false,
                ],
                "parameters" => [
                    "cps" => 0,
                    "autosprint" => false,
                ],
                "mute" => [
                    "reason" => "",
                    "time" => time(),
                    "staff" => ""
                ]
            ]);
        }else{
            $this->setBaseProperties(PlayerUtils::TagtoArray($nbt->getCompoundTag("properties")));
        }
    }

    public function save(CompoundTag $tag)
    {
        $tag->setTag("properties", PlayerUtils::arraytoTag($this->getPropertiesList()));
    }

}