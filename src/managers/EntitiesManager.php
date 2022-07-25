<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\entities\list\FishingHook;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

final class EntitiesManager extends Managers
{
    public function getAll(): array
    {
        return [
            [FishingHook::class, function (World $world, CompoundTag $nbt): FishingHook {
                return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null);
            }, ["FishingRod", "minecraft:fishingrod"], EntityLegacyIds::FISHING_HOOK]
        ];
    }

    public function init(): void
    {
        foreach (self::getAll() as [$class, $callback, $names, $id]) {
            EntityFactory::getInstance()->register($class, $callback, $names, $id);
            Core::getInstance()->getLogger()->notice("[ENTITIES] Entity: $names[0] Loaded");
        }
    }
}