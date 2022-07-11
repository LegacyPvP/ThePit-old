<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\CooldownManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent as ClassEvent;
use pocketmine\item\Sword;

final class PlayerItemUseEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        if(CooldownManager::hasCooldown($event->getItem())){
            $event->getPlayer()->sendTip($event->getPlayer()->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => CooldownManager::getCooldown($event->getItem()) - time()])->__toString());
            $event->cancel();
        }
        else {
            if($event->getItem() instanceof Sword) return;
            $event->getPlayer()->getInventory()->setItemInHand(CooldownManager::setCooldown($event->getItem(), null));
        }
    }
}