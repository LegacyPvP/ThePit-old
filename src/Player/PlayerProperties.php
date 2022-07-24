<?php

namespace Legacy\ThePit\Player;

use Legacy\ThePit\Managers\Managers;
use Legacy\ThePit\Traits\PropertiesTrait;
use Legacy\ThePit\Utils\CurrencyUtils;
use Legacy\ThePit\Utils\PlayerUtils;
use Legacy\ThePit\Utils\PrestigesUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

final class PlayerProperties
{
    use PropertiesTrait;

    public function __construct(public LegacyPlayer $player)
    {
        if (!($nbt = $this->player->getNBT())->getCompoundTag('properties') || empty($nbt->getCompoundTag("properties")->getValue())) {
            $this->setBaseProperties([
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
                    "combat" => false,
                    "combat_players" => []
                ],
                "settings" => [
                    "cps" => 0,
                    "autosprint" => false,
                    "blocked_players" => [],
                    "allow_private_messages" => true,
                    "show-join-message" => true,
                    "show-leave-message" => true,
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
                    "helmet" => ItemFactory::getInstance()->get(ItemIds::CHAIN_HELMET),
                    "chestplate" => ItemFactory::getInstance()->get(ItemIds::CHAIN_CHESTPLATE),
                    "leggings" => ItemFactory::getInstance()->get(ItemIds::CHAIN_LEGGINGS),
                    "boots" => ItemFactory::getInstance()->get(ItemIds::CHAIN_BOOTS),

                    "sword" => ItemFactory::getInstance()->get(ItemIds::STONE_SWORD),
                    "bow" => ItemFactory::getInstance()->get(ItemIds::BOW),
                    "arrow" => ItemFactory::getInstance()->get(ItemIds::ARROW, 0, 16),

                    "rod" => ItemFactory::getInstance()->get(ItemIds::AIR), //1k gold
                    "bucket" => ItemFactory::getInstance()->get(ItemIds::AIR), //500 gold
                    "snowball" => ItemFactory::getInstance()->get(ItemIds::AIR), //500 gold
                    "blocks" => ItemFactory::getInstance()->get(ItemIds::AIR), //1k gold
                    "flap" => ItemFactory::getInstance()->get(ItemIds::AIR), //1,5k gold
                    "nemo" => ItemFactory::getInstance()->get(ItemIds::AIR), //1,5k gold
                ]
            ]);
        } else {
            $this->setBaseProperties(PlayerUtils::TagtoArray($nbt->getCompoundTag("properties")));
        }
    }

    public function save(CompoundTag $tag)
    {
        $tag->setTag("properties", PlayerUtils::arraytoTag($this->getPropertiesList()));
    }

}