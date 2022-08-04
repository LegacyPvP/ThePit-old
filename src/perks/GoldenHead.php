<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\listeners\PlayerDeathEvent;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use Legacy\ThePit\items\list\GoldenHead as Item;

final class GoldenHead extends Perk
{
    final public function onEvent(): string {
        return PlayerDeathEvent::class;
    }

    final public function start(Player $player): void
    {
        $player->getInventory()->addItem(new Item(new ItemIdentifier(ItemIds::APPLE_ENCHANTED, 0), "Golden Head"));
    }

    final public function canStart(LegacyPlayer $player): bool
    {
        foreach($player->getInventory()->getContents() as $item){
            if($item::class === Item::class){
                return true;
            }
        }
        return false;
    }
}