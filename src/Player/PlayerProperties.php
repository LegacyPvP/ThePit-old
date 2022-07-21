<?php

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Objects\Prestige;
use Legacy\ThePit\Traits\PropertiesTrait;
use Legacy\ThePit\Utils\PlayerUtils;
use Legacy\ThePit\Utils\SpellUtils;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;

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
                    "killstreak" => 0,
                    "prestige" => Prestige::PRESTIGE_0,
                    "prime" => 0,
                ],
                "money" => [
                    "gold" => 0,
                    "credits" => 0,
                    "etoiles" => 0,
                    "votecoins" => 0,
                ],
                "infos" => [
                    "ip" => "",
                    "platform" => "test",
                    "rank" => RanksManager::getDefaultRank()?->getName(),
                ],
                "status" => [
                    "nightvision" => false,
                    "freezed" => false,
                    "combat" => false,
                    "combat_players" => []
                ],
                "settings" => [
                    "cps" => 0,
                    "autosprint" => false,
                    "blocked_players" => [],
                    "allow_private_messages" => true,
                ],
                "mute" => [
                    "reason" => "",
                    "time" => time(),
                    "staff" => ""
                ],
                "saved" => [
                    "last_position" => new Vector3(0, 0, 0),
                    "last_login" => "",
                    "last_logout" => ""
                ],
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