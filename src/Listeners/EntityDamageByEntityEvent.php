<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Core;
use Legacy\ThePit\Items\List\Nemo;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent as ClassEvent;
use pocketmine\event\Listener;

final class EntityDamageByEntityEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        if(($damager = $event->getDamager()) instanceof LegacyPlayer){
            $item = $event->getDamager()->getInventory()->getItemInHand();
            switch(true){
                case $item instanceof Nemo:
                    $vector = $damager->getDirectionVector();
                    $event->getEntity()->knockBack($vector->getX(), $vector->getZ(), Core::getInstance()->getConfig()->getNested("items.nemo.horizontal", 2), Core::getInstance()->getConfig()->getNested("items.nemo.vertical", 0.50));
                    $event->setKnockBack(0);
                    break;
            }
        }
    }
}