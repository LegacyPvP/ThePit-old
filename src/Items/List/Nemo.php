<?php

namespace Legacy\ThePit\Items\List;

use Legacy\ThePit\Items\CustomSword;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;

final class Nemo extends Item
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct($identifier, $name);
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}