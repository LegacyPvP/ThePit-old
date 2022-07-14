<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Items\List\Flap;
use Legacy\ThePit\Items\List\Nemo;
use Legacy\ThePit\Items\List\Spell;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

abstract class ItemsManager
{
    public static function getItems(): array {
        return [
            new Nemo(new ItemIdentifier(ItemIds::CLOWNFISH, 0), "Nemo"),
            new Flap(new ItemIdentifier(ItemIds::FEATHER, 0), "Flap"),
            new Spell(new ItemIdentifier(ItemIds::NETHER_STAR, 0), "Spell")
        ];
    }

    public static function initItems(): void {
        foreach (self::getItems() as $item){
            ItemFactory::getInstance()->register($item, true);
            Core::getInstance()->getLogger()->notice("[ITEMS] Item: {$item->getName()} Loaded");
        }
    }

}