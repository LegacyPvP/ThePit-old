<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent as ClassEvent;
use pocketmine\player\Player;
use pocketmine\Server;

final class PlayerDeathEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $event->setDeathMessage("");
        ($cause = ($player = $event->getPlayer())->getLastDamageCause());
        $killer = $cause->getDamager();
        if ($cause instanceof EntityDamageByEntityEvent and $killer instanceof LegacyPlayer) {
            $killer->getLanguage()->getMessage("messages.kill", ["{player}" => $player->getName()])->send($killer);
            $player->getLanguage()->getMessage("messages.death.killed", ["{player}" => $killer->getName()])->send($player);

            # TODO: KILLSTREAK
            $killstreak = ($killer->getPlayerProperties()->getNestedProperties("stats.killstreak") ?? 0) + 1;
            $killer->getPlayerProperties()->setNestedProperties("stats.killstreak", $killstreak);
            if($killstreak % 10 === 0){
                $array = [50, 75, 100];
                $gold = ($killer->getPlayerProperties()->getNestedProperties("money.gold") ?? 0) + $array[array_rand($array)];
                $killer->getPlayerProperties()->setNestedProperties("money.gold", $gold);
                $killer->getLanguage()->getMessage("messages.killstreak.player", ["{killstreak}" => $killstreak, "{gold}" => $gold])->send($killer);
                Server::getInstance()->broadcastMessage($killer->getLanguage()->getMessage("messages.killstreak.broadcast", ["{player}" => $killer->getName(), "{killstreak}" => $killstreak, "{gold}" => $gold])->__toString());
            }

             
        }
    }
}