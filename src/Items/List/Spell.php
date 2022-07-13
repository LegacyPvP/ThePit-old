<?php

namespace Legacy\ThePit\Items\List;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class Spell extends Item
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct($identifier, $name);
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }

    public function switchMode(LegacyPlayer $player): void
    {
        //TODO: Implement switchMode() method.
    }
}