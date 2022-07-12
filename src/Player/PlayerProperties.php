<?php

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Traits\PropertiesTrait;
use Legacy\ThePit\Utils\PlayerUtils;

final class PlayerProperties {
    use PropertiesTrait;

    public function __construct(LegacyPlayer $player)
    {
        if(!$player->getNBT()->getCompoundTag('properties')){
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
                ],
                "infos" => [
                    "ip" => "",
                    "platform" => "test",
                    "rank" => RanksManager::getDefaultRank()->getName(),
                ],
                "status" => [
                    "muted" => false,
                    "freezed" => false,
                ],
                "parameters" => [
                    "cps" => 0,
                    "autosprint" => false,
                ]
            ]);
        }
        $nbt = $player->getNBT();
        foreach ($this->getPropertiesList() as $name => $value){
            $nbt = PlayerUtils::valueToTag($name, $value, $nbt);
        }
    }
}