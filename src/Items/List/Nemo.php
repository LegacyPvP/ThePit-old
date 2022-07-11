<?php

namespace Legacy\ThePit\Items\List;

use Legacy\ThePit\Items\CustomSword;
use pocketmine\entity\Entity;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;

final class Nemo extends CustomSword
{
    public function __construct(ItemIdentifier $identifier, string $name, ToolTier $tier, string $textureName, int $durability, int $attackPoints)
    {
        parent::__construct($identifier, $name, $tier, $textureName, $durability, $attackPoints);
    }
}