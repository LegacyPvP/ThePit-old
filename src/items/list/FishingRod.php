<?php

namespace Legacy\ThePit\items\list;

use Legacy\ThePit\entities\list\FishingHook;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class FishingRod extends Tool
{
    public function onAttackEntity(Entity $victim): bool
    {
        return $this->applyDamage(1);
    }

    protected function createHook(LegacyPlayer $player): void
    {
        $location = clone $player->getLocation();
        $hook = new FishingHook(Location::fromObject(
            $player->getEyePos(),
            $player->getWorld(),
            ($location->yaw > 180 ? 360 : 0) - $location->yaw,
            -$location->pitch
        ), $player);
        $hook->setMotion($player->getDirectionVector());
        $player->setFishing($hook);
        $hook->spawnToAll();

    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        if (!$player instanceof LegacyPlayer) return ItemUseResult::NONE();
        if ($player->getFishingHook() != null) {
            $player->getFishingHook()->delete();
            return ItemUseResult::SUCCESS();
        }
        $this->createHook($player);
        // TODO: $this->applyDamage(1); ???
        return ItemUseResult::SUCCESS();
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }

    public function getMaxDurability(): int
    {
        return 65;
    }

    public function getFuelTime(): int
    {
        return 300;
    }
}