<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Items\List\Nemo;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

abstract class ItemsManager
{
    public static function getItems(): array {
        return [
            new Nemo(new ItemIdentifier(ItemIds::CLOWNFISH, 0), "Nemo"),
        ];
    }

    public static function initItems(): void {
        foreach (self::getItems() as $item){
            ItemFactory::getInstance()->register($item, true);
        }
    }

}