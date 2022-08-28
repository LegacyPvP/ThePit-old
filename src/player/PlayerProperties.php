<?php

namespace Legacy\ThePit\player;

use Legacy\ThePit\databases\SQLDatabase;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\traits\PropertiesTrait;
use Legacy\ThePit\utils\CurrencyUtils;
use Legacy\ThePit\utils\PlayerUtils;
use Legacy\ThePit\utils\PrestigesUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

final class PlayerProperties
{
    use PropertiesTrait;

    public function __construct(public LegacyPlayer $player)
    {
        $prop = [
            "stats" => [
                "kills" => 0,
                "deaths" => 0,
                "kdr" => 0.0,
                "level" => 1,
                "xp" => 0,
                "killstreak" => 0,
                "prestige" => PrestigesUtils::PRESTIGE_0,
                "prime" => 0,
            ],
            "money" => [
                CurrencyUtils::GOLD => 0,
                CurrencyUtils::CREDITS => 0,
                CurrencyUtils::STARS => 0,
                CurrencyUtils::VOTECOINS => 0,
            ],
            "infos" => [
                "ip" => "",
                "platform" => "test",
                "rank" => Managers::RANKS()->getDefaultRank()?->getName(),
            ],
            "status" => [
                "nightvision" => false,
                "freezed" => false,
            ],
            "settings" => [
                "cps" => 0,
                "autosprint" => false,
                "blocked_players" => [],
                "allow_private_messages" => true,
                "show-join-message" => true,
                "show-leave-message" => true,
            ],
            "keys" => [

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
            "inventory" => [
                "helmet" => 1,
                "chestplate" => 1,
                "leggings" => 1,
                "boots" => 1,

                "sword" => 1,
                "bow" => 1,
                "arrow" => 1,

                "hook" => 0, //1k gold
                "bucket_lava" => 0, //1500 gold
                "snowball" => 0, //500 gold
                "block" => 0, //1k gold
                "flap" => 0, //1,5k gold
                "nemo" => 0, //1,5k gold
            ],
            "quests" => [],
        ];
        if (!($nbt = $this->player->getNBT())->getCompoundTag('properties') || empty($nbt->getCompoundTag("properties")?->getValue())) {
            $this->setBaseProperties($prop);
        } else {
            $this->setBaseProperties($this->addExtraProperties($prop, PlayerUtils::TagToArray($nbt->getCompoundTag("properties"))));
        }
    }

    public function save(CompoundTag $tag)
    {
        SQLDatabase::getDatabase()->asyncInsert("user.update", $this->getCleanedProperties($this->getPropertiesList()));
        $tag->setTag("properties", PlayerUtils::arraytoTag($this->getPropertiesList()));
    }

    public function getCleanedProperties(array $array): array
    {
        $cleaned = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = $this->getCleanedProperties($value);
            } else {
                $cleaned[$key] = $value;
            }
        }
        return $cleaned;
    }

    public function addExtraProperties(array $fullprops, array $actualprops): array
    {
        foreach ($fullprops as $key => $value) {
            if (!isset($actualprops[$key])) {
                $actualprops[$key] = $value;
            } else {
                if (is_array($value)) {
                    $this->addExtraProperties($value, $actualprops[$key]);
                }
            }
        }
        return $actualprops;
    }
}