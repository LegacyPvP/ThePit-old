<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\CurrencyUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;

final class BountyHunter extends Perk
{
    final public function start(PlayerDeathEvent $event): void
    {
        if(!($cause = $event->getPlayer()->getLastDamageCause()) instanceof EntityDamageByEntityEvent) return;
        if(!($killer = $cause->getDamager()) instanceof LegacyPlayer) return;
        $killer->getCurrencyProvider()->add(CurrencyUtils::GOLD, $event->getPlayer()->getPlayerProperties()->getNestedProperties("stats.prime") ?? 0);
    }

    final public function canStart(PlayerDeathEvent $event){
        return true;
    }

    final public function onEvent(): string
    {
        return PlayerDeathEvent::class;
    }
}