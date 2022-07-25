<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent as ClassEvent;

final class PlayerQuitEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $event->setQuitMessage("");
        $player = $event->getPlayer();
        if ($player instanceof LegacyPlayer) {
            if ($player->getInCache("combat", false)) {
                if ($event->getQuitReason() === "Server Closed" or $event->getQuitReason() === ServerUtils::RESTART_REASON or $event->getQuitReason()) {
                    $player->save();
                    $player->getPlayerProperties()->setNestedProperties("saved.last_logout", time());
                    $player->getPlayerProperties()->setNestedProperties("status.combat", false);
                } else {
                    $player->getPlayerProperties()->setNestedProperties("saved.last_position", $player->getPosition());
                    $player->getPlayerProperties()->setNestedProperties("saved.last_logout", time());
                    $player->dropInventory();
                }
            }
        }

        foreach($player->getServer()->getOnlinePlayers() as $_player){
            if($_player instanceof LegacyPlayer){
                if($_player->getPlayerProperties()->getNestedProperties("settings.show-leave-message")){
                    $_player->getLanguage()->getMessage("messages.player-quit", ["{player}" => $player->getName()], ServerUtils::PREFIX_4)->sendPopup($_player);
                }
            }
        }
    }

}