<?php

namespace Legacy\ThePit\Items\List;

use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\SpellUtils;
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

    public static function openSpell(Player $player): void
    {
        if($player->getInventory()->contains(SpellUtils::getBookItem())){
            $player->getInventory()->removeItem(SpellUtils::getBookItem());
            $player->getInventory()->addItem(SpellUtils::randomSpell());
        }
    }
}