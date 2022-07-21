<?php

namespace Legacy\ThePit\Utils;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

abstract class SpellUtils {

    const SPELL_ATTRACTION = "spell_attraction";
    const SPELL_REPULSION = "spell_repulsion";
    const SPELL_LIGHTNING = "spell_lightning";
    const SPELL_REVERSE = "spell_reverse";
    const SPELL_BLINDNESS = "spell_blindness";
    const SPELL_TELEPORT = "spell_teleport";
    const SPELL_SPEED = "spell_speed";
    const SPELL_HEALTH = "spell_health";
    //TODO les rajouter tous

    const SPELL_ATTRACTION_NAME = "§r§dSort d'attraction";
    const SPELL_REPULSION_NAME = "§r§dSort de répulsion";
    const SPELL_LIGHTNING_NAME = "§r§dSort de foudre";
    const SPELL_REVERSE_NAME = "§r§dSort d'inversement";
    const SPELL_BLINDNESS_NAME = "§r§dSort d'aveuglement";
    const SPELL_TELEPORT_NAME = "§r§dSort de téléportation";
    const SPELL_SPEED_NAME = "§r§dSort de rapidité";
    const SPELL_HEALTH_NAME = "§r§dSort de soin";
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
        return ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK, 0, 1)->setCustomName($name_spell);
    }
}
