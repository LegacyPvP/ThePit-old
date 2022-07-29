<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\items\list\FishingRod;
use Legacy\ThePit\items\list\Flap;
use Legacy\ThePit\items\list\Nemo;
use Legacy\ThePit\items\list\Spell;
use Legacy\ThePit\test\Bow;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

final class ItemsManager extends Managers
{
    public function getAll(): array
    {
        return [
            new Nemo(new ItemIdentifier(ItemIds::CLOWNFISH, 0), "Nemo"),
            new Flap(new ItemIdentifier(ItemIds::FEATHER, 0), "Flap"),
            new Spell(new ItemIdentifier(ItemIds::ENCHANTED_BOOK, 0), "Spell"),
            new FishingRod(new ItemIdentifier(ItemIds::FISHING_ROD, 0), "Fishing Rod"),
        ];
    }

    public function init(): void
    {
        foreach (self::getAll() as $item) {
            ItemFactory::getInstance()->register($item, true);
            Core::getInstance()->getLogger()->notice("[ITEMS] Item: {$item->getName()} Loaded");
        }
    }

}