<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use Legacy\ThePit\items\list\GoldenHead as Item;

final class GoldenHead extends Perk
{
    final public function onEvent(): string {
        return PlayerDeathEvent::class;
    }

    final public function start(PlayerDeathEvent $event): void
    {
        if(!($cause = $event->getPlayer()->getLastDamageCause()) instanceof EntityDamageByEntityEvent) return;
        if(!($killer = $cause->getDamager()) instanceof LegacyPlayer) return;
        $killer->getInventory()->addItem(new Item(new ItemIdentifier(ItemIds::APPLE_ENCHANTED, 0), "Golden Head"));
    }

    final public function canStart(PlayerDeathEvent $event): bool
    {
        if(!($cause = $event->getPlayer()->getLastDamageCause()) instanceof EntityDamageByEntityEvent) return false;
        if(!($killer = $cause->getDamager()) instanceof LegacyPlayer) return false;
        foreach($killer->getInventory()->getContents() as $item){
            if($item::class === Item::class){
                return true;
            }
        }
        return false;
    }
}