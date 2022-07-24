<?php

namespace Legacy\ThePit\Items\List;

use Legacy\ThePit\Entities\List\FishingHook;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Entity;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FishingRod extends Tool
{
    public function onAttackEntity(Entity $victim): bool
    {
        return $this->applyDamage(1);
    }

    protected function createHook(LegacyPlayer $player): void
    {
        $hook = new FishingHook($player->getLocation(), $player);
        $player->setFishing($hook);
        $hook->spawnToAll();
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        if(!$player instanceof LegacyPlayer) return ItemUseResult::NONE();
        $player->broadcastAnimation(new ArmSwingAnimation($player));
        if($player->getFishingHook() != null){
            $player->getFishingHook()->delete();
            return ItemUseResult::SUCCESS();
        } else {
            $this->createHook($player);
            return ItemUseResult::SUCCESS();
        }
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