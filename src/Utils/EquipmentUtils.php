<?php

namespace Legacy\ThePit\utils;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

abstract class EquipmentUtils {

    const HELMET = 1, CHESTPLATE = 2, LEGGINGS = 3, BOOTS = 4;
    const SWORD = 1, BOW = 2, ARROW = 3;

    //ARMOR
    const ARMOR_CHAIN = 1, ARMOR_IRON = 2, ARMOR_DIAMOND = 3, ARMOR_EMERALD = 4, ARMOR_DIAMOND_ENCHANTED = 5, ARMOR_EMERALD_ENCHANTED = 6;

    //WEAPONS
    const SWORD_STONE = 1, SWORD_IRON = 2, SWORD_DIAMOND = 3, SWORD_EMERALD = 4, SWORD_DIAMOND_ENCHANTED = 5, SWORD_EMERALD_ENCHANTED = 6;

    //ASSETS

    public static function getArmorId(int $index, int $level): int
    {
        if ($index == self::HELMET)
            return match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_HELMET,
                self::ARMOR_IRON => ItemIds::IRON_HELMET,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_HELMET,
            };
        else if ($index == self::CHESTPLATE){
            return match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_CHESTPLATE,
                self::ARMOR_IRON => ItemIds::IRON_CHESTPLATE,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_CHESTPLATE,
            };
        }
        else if ($index == self::LEGGINGS){
            return match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_LEGGINGS,
                self::ARMOR_IRON => ItemIds::IRON_LEGGINGS,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_LEGGINGS,
            };
        }
        else if ($index == self::BOOTS){
            return match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_BOOTS,
                self::ARMOR_IRON => ItemIds::IRON_BOOTS,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_BOOTS,
            };
        }
        return ItemIds::AIR;
    }

    public static function getWeaponId(int $index, int $level): int
    {
        if ($index == self::SWORD)
            return match ($level) {
                self::SWORD_STONE => ItemIds::CHAIN_HELMET,
                self::SWORD_IRON => ItemIds::IRON_HELMET,
                self::SWORD_DIAMOND => ItemIds::DIAMOND_HELMET,
            };
        return ItemIds::AIR;
    }
}