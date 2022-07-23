<?php

namespace Legacy\ThePit\Utils;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

abstract class PlayerUtils
{
    private static array $properties;

    public static function valueToTag(string $property, mixed $value, ?CompoundTag $nbt = null): CompoundTag
    {
        if (!$nbt) $nbt = new CompoundTag();
        return match (gettype($value)) {
            "integer" => $nbt->setInt($property, $value),
            "double" => $nbt->setDouble($property, $value),
            "string" => $nbt->setString($property, $value),
            "boolean" => $nbt->setByte($property, $value),
            "array" => $nbt->setTag($property, self::arraytoTag($value)),
        };
    }

    public static function tagtoArray(CompoundTag|ListTag $nbt, $name = null): array
    {
        foreach ($nbt->getValue() as $key => $value) {
            if ($value instanceof CompoundTag || $value instanceof ListTag) {
                self::tagtoArray($value, array_search($value, $nbt->getValue(), true));
            } else {
                $name === null ? self::$properties[$key] = $value->getValue() : self::$properties[$name][$key] = $value->getValue();

            }
        }
        return self::$properties;
    }

    public static function arraytoTag(array $array): CompoundTag
    {
        $nbt = new CompoundTag();
        foreach ($array as $property => $value) {
            match (gettype($value)) {
                "integer" => $nbt->setInt($property, $value),
                "double" => $nbt->setDouble($property, $value),
                "string" => $nbt->setString($property, $value),
                "boolean" => $nbt->setByte($property, $value),
                "array" => $nbt->setTag($property, self::arrayToTag($value)),
                default => null
            };
        }
        return $nbt;
    }
}