<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\CustomItemManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent as ClassEvent;
use pocketmine\Server;

final class PlayerQuitEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $event->setQuitMessage("");
        $player = $event->getPlayer();
        if($player instanceof LegacyPlayer){
            if($player->isInCombat()){
                if($event->getQuitReason() === "Server Closed" or $event->getQuitReason() === ServerUtils::RESTART_REASON or $event->getQuitReason()){
                    $player->save();
                    $player->getPlayerProperties()->setNestedProperties("saved.last_logout", time());
                    $player->getPlayerProperties()->setNestedProperties("status.combat", false);
                }else{
                    $player->getPlayerProperties()->setNestedProperties("saved.last_position", $player->getPosition());
                    $player->getPlayerProperties()->setNestedProperties("saved.last_logout", time());
                    $player->dropInventory();
                }
            }
        }
    }

}