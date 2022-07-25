<?php

namespace Legacy\ThePit\items\list;

use Legacy\ThePit\managers\Managers;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class Flap extends Item
{
    public function __construct(ItemIdentifier $identifier, string $name = "Flap")
    {
        parent::__construct($identifier, $name);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        $motion = $player->getMotion();
        $motion->x += $directionVector->x * (float)Managers::DATA()->get("config")->getNested("items.flap.horizontal", 1.0);
        $motion->y += $directionVector->y * (float)Managers::DATA()->get("config")->getNested("items.flap.vertical", 1.0); // TODO: ($directionVector->y > 0.85 ? 1 : 0.15) * (float)managers::DATA()->get("config")->getNested("items.flap.vertical", 1.0)
        $motion->z += $directionVector->z * (float)Managers::DATA()->get("config")->getNested("items.flap.horizontal", 1.0);
        $player->setMotion($motion);
        return parent::onClickAir($player, $directionVector);
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}