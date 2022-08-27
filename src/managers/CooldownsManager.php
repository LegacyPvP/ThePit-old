<?php

namespace Legacy\ThePit\managers;

use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;

final class CooldownsManager extends Managers
{

    public static function getCooldownConfig(int $item): bool
    {
        return Managers::DATA()->get("config")->getNested("cooldowns.$item", 0);
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