<?php
namespace Legacy\ThePit\Utils;

use pocketmine\nbt\tag\CompoundTag;

abstract class PlayerUtils
{
    public static function valueToTag(string $property, mixed $value, ?CompoundTag $nbt = null): CompoundTag{
        if(!$nbt) $nbt = new CompoundTag();
        return match (gettype($value)){
            "integer" => $nbt->setInt($property, $value),
            "double" => $nbt->setDouble($property, $value),
            "string" => $nbt->setString($property, $value),
            "boolean" => $nbt->setByte($property, $value),
            "array" => $nbt->setTag($property, self::arraytoTag($value)),
        };
    }

    public static function arraytoTag(array $array): CompoundTag {
        $nbt = new CompoundTag();
        foreach($array as $property => $value){
            match (gettype($value)){
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