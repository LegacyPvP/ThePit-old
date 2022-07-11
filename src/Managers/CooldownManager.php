<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;

abstract class CooldownManager
{
    public static function getCooldownConfig(int $item): bool
    {
        return Core::getInstance()->getConfig()->getNested("cooldowns.$item", 0);
    }

    public static function setCooldown(Item $item, ?int $value): Item
    {
        $cooldown = fn(int $id) => $value ?? self::getCooldownConfig($id);
        $item->getNamedTag()->setTag('cooldown', new IntTag(time() + $cooldown($item->getId())));
        return $item;
    }

    public static function getCooldown(Item $item): int
    {
        return ($item->getNamedTag()->getTag('cooldown')?->getValue() ?? time());
    }

    public static function hasCooldown(Item $item): bool
    {
        return self::getCooldown($item) > time();
    }

}