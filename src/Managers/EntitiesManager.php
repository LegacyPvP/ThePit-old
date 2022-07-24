<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Entities\List\FishingHook;
use Legacy\ThePit\Items\List\FishingRod;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

abstract class EntitiesManager
{
    public static function getEntities(): array
    {
        return [
            [FishingHook::class, function (World $world, CompoundTag $nbt): FishingHook {
                return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null);
            }, ["FishingRod", "minecraft:fishingrod"], EntityLegacyIds::FISHING_HOOK]
        ];
    }

    public static function initEntities(): void
    {
        foreach (self::getEntities() as [$class, $callback, $names, $id]) {
            EntityFactory::getInstance()->register($class, $callback, $names, $id);
            Core::getInstance()->getLogger()->notice("[ENTITIES] Entity: {$names[0]} Loaded");
        }

    }
}