<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\listeners\events\PlayerCollectGoldEvent;
use Legacy\ThePit\utils\CurrencyUtils;
use pocketmine\event\player\PlayerDeathEvent;

final class Nabbit extends Perk
{
    public function start(PlayerCollectGoldEvent $event): void{
        $event->getPlayer()->getCurrencyProvider()->add(CurrencyUtils::GOLD, $event->getGold());
        $event->getPlayer()->sendMessage("§6Vous avez reçu §e{$event->getGold()} pièces d'or bonus §6grâce à l'atout Nabbit");
    }

    final public function canStart(PlayerCollectGoldEvent $event){
        return true;
    }

    public function onEvent(): string
    {
        return PlayerCollectGoldEvent::class;
    }
}