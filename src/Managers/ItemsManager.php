<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Items\List\FishingRod;
use Legacy\ThePit\Items\List\Flap;
use Legacy\ThePit\Items\List\Nemo;
use Legacy\ThePit\Items\List\Spell;
use Legacy\ThePit\Test\Bow;
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
            new Bow(new ItemIdentifier(ItemIds::BOW, 0), "Bow"),
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