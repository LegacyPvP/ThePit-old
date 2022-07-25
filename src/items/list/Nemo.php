<?php

namespace Legacy\ThePit\items\list;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;

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