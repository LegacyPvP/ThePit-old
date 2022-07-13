<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Core;
use Legacy\ThePit\Items\List\Nemo;
use Legacy\ThePit\Items\List\Spell;
use Legacy\ThePit\Managers\CooldownManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent as ClassEvent;
use pocketmine\event\Listener;

final class EntityDamageByEntityEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $event->setKnockBack(0);
        if(($damager = $event->getDamager()) instanceof LegacyPlayer){
            $vector = $damager->getDirectionVector();
            $item = $event->getDamager()->getInventory()->getItemInHand();
            if($damager->isImmobile()){
                $event->cancel();
                return;
            }
            switch(true){
                case $item instanceof Nemo:
                    if(CooldownManager::hasCooldown($item)){
                        $damager->sendTip($damager->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => CooldownManager::getCooldown($item) - time()])->__toString());
                        $event->cancel();
                    }
                    else if(CooldownManager::getCooldownConfig($item->getId())){
                        $event->getEntity()->knockBack($vector->getX(), $vector->getZ(), Core::getInstance()->getConfig()->getNested("items.nemo.horizontal", 2), Core::getInstance()->getConfig()->getNested("items.nemo.vertical", 0.50));
                        $item = CooldownManager::setCooldown($item, null);
                        $damager->getInventory()->setItemInHand($item);
                    }
                    break;
                case $item instanceof Spell:
                    if(CooldownManager::hasCooldown($item)){
                        $damager->sendTip($damager->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => CooldownManager::getCooldown($item) - time()])->__toString());
                        $event->cancel();
                    }
                    else if(CooldownManager::getCooldownConfig($item->getId())){
                        $event->getEntity()->knockBack($vector->getX(), $vector->getZ(), Core::getInstance()->getConfig()->getNested("items.nemo.horizontal", 2), Core::getInstance()->getConfig()->getNested("items.nemo.vertical", 0.50));
                        $item = CooldownManager::setCooldown($item, null);
                        $damager->getInventory()->setItemInHand($item);
                    }
                default:
                    break;
            }
            $event->setAttackCooldown(Core::getInstance()->getConfig()->getNested("knockback.attack_cooldown", 10));
        }
    }
}