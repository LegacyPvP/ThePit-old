<?php

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\GradesManager;
use Legacy\ThePit\Traits\PropertiesTrait;

final class PlayerProperties {
    use PropertiesTrait;

    public function __construct(LegacyPlayer $player)
    {
        $nbt = $player->getNBT();
        if($nbt->getTag("parameters")){
        }
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
                "grade" => GradesManager::getDefaultGrade()->getName(),
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
}