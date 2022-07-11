<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent as ClassEvent;

class PlayerChatEvent implements Listener
{
    /**
     * @param ClassEvent $event
     * @priority LOWEST
     */
    public function onEvent(ClassEvent $event){
        $player = $event->getPlayer();
        if($player instanceof LegacyPlayer){
            $event->setFormat($player->getGrade()->getFormat([
                "{player}" => $player->getName(),
                "{chat}" => $event->getMessage(),
            ]));
            $event->setMessage("");
            if($player->getPlayerProperties()->getNestedProperties("status.muted") ?? false){
                $event->cancel();
            }
        }
    }
}