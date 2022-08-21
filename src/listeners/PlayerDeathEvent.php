<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\events\Events;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\CurrencyUtils;
use Legacy\ThePit\utils\StatsUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent as ClassEvent;

final class PlayerDeathEvent implements Listener
{
    final public function onEvent(ClassEvent $event): void
    {
        $player = $event->getPlayer();
        $cause = $event->getPlayer()->getLastDamageCause();
        $killer = $cause->getDamager();
        $event->setDeathMessage("");
        if($cause instanceof EntityDamageByEntityEvent and $killer instanceof LegacyPlayer){
            $killer->getLanguage()->getMessage("messages.kill", ["{player}" => $player->getName()])->send($killer);
            $player->getLanguage()->getMessage("messages.death.killed", ["{player}" => $killer->getName()])->send($player);
            $killer->getStatsProvider()->add(StatsUtils::KILLSTREAK, 1);
            Managers::EVENTS()->getCurrentEvent()::class === Events::BOUNTY()::class
                ? $killer->getStatsProvider()->add(StatsUtils::PRIME, $player->getStatsProvider()->get(StatsUtils::PRIME))
                : $killer->getCurrencyProvider()->add(CurrencyUtils::GOLD, $player->getStatsProvider()->get(StatsUtils::PRIME));
            $player->getStatsProvider()->set(StatsUtils::KILLSTREAK, 0);
            $player->getStatsProvider()->set(StatsUtils::PRIME, 0);
            $player->setStuff();
            $killer->setStuff();

            if (($killstreak = $killer->getStatsProvider()->get(StatsUtils::KILLSTREAK)) % 10 === 0) {
                $array = [50, 75, 100];
                $prime = $killer->getStatsProvider()->get(StatsUtils::PRIME) + $array[array_rand($array)];
                $add = $array[array_rand($array)];
                $killer->getStatsProvider()->set(StatsUtils::PRIME, $prime);
                $killer->getLanguage()->getMessage("messages.killstreak.player", ["{killstreak}" => $killstreak, "{prime}" => $add])->send($killer);
                foreach($killer->getServer()->getOnlinePlayers() as $player){
                    $player->getLanguage()->getMessage("messages.killstreak.broadcast", ["{player}" => $killer->getName(), "{killstreak}" => $killstreak, "{prime}" => $add])->send($player);
                }
            }
        }
    }
}