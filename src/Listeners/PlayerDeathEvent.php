<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent as ClassEvent;

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
            $killer->getPlayerProperties()->setNestedProperties("stats.prime", $killer->getPlayerProperties()->getNestedProperties("stats.prime") + $player->getPlayerProperties()->getNestedProperties("stats.prime"));
            if($killstreak % 10 === 0){
                $array = [50, 75, 100];
                $prime = ($killer->getPlayerProperties()->getNestedProperties("stats.prime") ?? 0) + $array[array_rand($array)];
                $add = $array[array_rand($array)];
                $killer->getPlayerProperties()->setNestedProperties("stats.prime", $prime);
                $killer->getLanguage()->getMessage("messages.killstreak.player", ["{killstreak}" => $killstreak, "{prime}" => $add])->send($killer);
                $killer->getLanguage()->getMessage("messages.killstreak.broadcast", ["{player}" => $killer->getName(), "{killstreak}" => $killstreak, "{prime}" => $add]);
            }
            $player->getPlayerProperties()->setNestedProperties("stats.killstreak", 0);
            $player->getPlayerProperties()->setNestedProperties("stats.prime", 0);
        }
    }
}