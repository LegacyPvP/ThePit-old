<?php

namespace Legacy\ThePit\Utils;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class SpellUtils {

    const SPELL_ATTRACTION = "spell_attraction";
    const SPELL_REPULSION = "spell_repulsion";
    const SPELL_LIGHTNING = "spell_lightning";
    const SPELL_REVERSE = "spell_reverse";
    const SPELL_BLINDNESS = "spell_blindness";
    const SPELL_TELEPORT = "spell_teleport";
    const SPELL_SPEED = "spell_speed";
    const SPELL_HEALTH = "spell_health";
    //TODO les rajouter tous

    const SPELL_ATTRACTION_NAME = "§l§dSort d'attraction";
    const SPELL_REPULSION_NAME = "§l§dSort de répulsion";
    const SPELL_LIGHTNING_NAME = "§l§dSort de foudre";
    const SPELL_REVERSE_NAME = "§l§dSort d'inversement";
    const SPELL_BLINDNESS_NAME = "§l§dSort d'aveuglement";
    const SPELL_TELEPORT_NAME = "§l§dSort de téléportation";
    const SPELL_SPEED_NAME = "§l§dSort de rapidité";
    const SPELL_HEALTH_NAME = "§l§dSort de soin";
    //TODO les rajouter tous

    public static function getBookItem(): Item {
        return ItemFactory::getInstance()->get(ItemIds::BOOK, 0, 1);
    }

    public static function randomSpell(): Item {
        $spells = [
            self::SPELL_ATTRACTION_NAME,
            self::SPELL_REPULSION_NAME,
            self::SPELL_LIGHTNING_NAME,
            self::SPELL_REVERSE_NAME,
            self::SPELL_BLINDNESS_NAME,
            self::SPELL_TELEPORT_NAME,
            self::SPELL_SPEED_NAME,
            self::SPELL_HEALTH_NAME
        ];
        $name_spell = $spells[array_rand($spells)];
        return ItemFactory::getInstance()->get(ItemIds::NETHER_STAR, 0, 1)->setCustomName($name_spell);
    }
}
