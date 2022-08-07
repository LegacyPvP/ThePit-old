<?php

namespace Legacy\ThePit\utils;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\block\BlockLegacyIds;

abstract class EquipmentUtils {

    const HELMET = 1, CHESTPLATE = 2, LEGGINGS = 3, BOOTS = 4;
    const SWORD = 1, BOW = 2, ARROW = 3;
    const HOOK = 1, BUCKET_LAVA = 2, SNOWBALL = 3, BLOCKS = 4, FLAP = 5, NEMO = 6;

    const ARMOR_CHAIN = 1, ARMOR_IRON = 2, ARMOR_DIAMOND = 3;
    const SWORD_STONE = 1, SWORD_IRON = 2, SWORD_DIAMOND = 3;
    const BOW_UNENCHANTED = 1, BOW_ENCHANTED = 2;
    const ARROW_16 = 1, ARROW_32 = 2, ARROW_64 = 3;
    const HOOK_NORMAL = 1;
    const BUCKET_LAVA_NORMAL = 1;
    const SNOWBALL_4 = 1, SNOWBALL_8;
    const BLOCKS_16 = 1, BLOCKS_32;
    const FLAP_NORMAL = 1;
    const NEMO_NORMAL = 1;


    public static function getArmorId(int $index, int $level): int
    {
        return match ($index) {
            self::HELMET => match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_HELMET,
                self::ARMOR_IRON => ItemIds::IRON_HELMET,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_HELMET,
            },
            self::CHESTPLATE => match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_CHESTPLATE,
                self::ARMOR_IRON => ItemIds::IRON_CHESTPLATE,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_CHESTPLATE,
            },
            self::LEGGINGS => match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_LEGGINGS,
                self::ARMOR_IRON => ItemIds::IRON_LEGGINGS,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_LEGGINGS,
            },
            self::BOOTS => match ($level) {
                self::ARMOR_CHAIN => ItemIds::CHAIN_BOOTS,
                self::ARMOR_IRON => ItemIds::IRON_BOOTS,
                self::ARMOR_DIAMOND => ItemIds::DIAMOND_BOOTS,
            },
            default => ItemIds::AIR,
        };
    }

    public static function getWeaponId(int $index, int $level): int|array
    {
        if ($index == self::SWORD)
            return match ($level) {
                self::SWORD_STONE => ItemIds::STONE_SWORD,
                self::SWORD_IRON => ItemIds::IRON_SWORD,
                self::SWORD_DIAMOND => ItemIds::DIAMOND_SWORD,
            };
        elseif($index == self::BOW)
            return match ($level) {
                self::BOW_UNENCHANTED => ItemIds::BOW,
                self::BOW_ENCHANTED => ItemIds::BOW,
            };
        elseif($index == self::ARROW)
            return match ($level) {
                self::ARROW_16 => [ItemIds::ARROW, 0, 16],
                self::ARROW_32 => [ItemIds::ARROW, 0, 32],
                self::ARROW_64 => [ItemIds::ARROW, 0, 64],
            };
        return ItemIds::AIR;
    }

    public static function getSupportId(int $index, int $level): int|array 
    {
        return match($index){
            self::HOOK => match($level){
                self::HOOK_SUPPORT => ItemIds::FISHING_ROD,
            },
            self::BUCKET_LAVA => match($level){
                self::BUCKET_LAVA_NORMAL => [ItemIds::BUCKET, 2],
            },
            self::SNOWBALL => match($level){
                self::SNOWBALL_4 => [ItemIds::SNOWBALL, 0, 4],
                self::SNOWBALL_8 => [ItemIds::SNOWBALL, 0, 8],
            },
            self::BLOCKS => match($level){
                self::BLOCKS_16 => [ItemIds::STONE, 0, 16],
                self::BLOCKS_32 => [ItemIds::STONE, 0, 32],
            },
            self::FLAP => match($level){
                self::FLAP_NORMAL => ItemIds::FEATHER,
            },
            self::NEMO => match($level){
                self::NEMO_NORMAL => ItemIds::CLOWNFISH,
            },
            default => ItemIds::AIR,
        }
    }
}