<?php

namespace Legacy\ThePit\events;

use Legacy\ThePit\listeners\events\PlayerCurrencyChangeEvent;
use Legacy\ThePit\listeners\events\PlayerStatsChangeEvent;
use Legacy\ThePit\utils\CurrencyUtils;
use pocketmine\event\Event;

final class DoubleReward extends MinorEvent
{
    public function handle(Event $event): void {
        if($event instanceof PlayerCurrencyChangeEvent){
            if($event->getCurrency() <> CurrencyUtils::GOLD) return;
            $event->add($event->getValue());
        }
        else if($event instanceof PlayerStatsChangeEvent){
            if($event->getType() === "xp"){
                $event->setValue($event->getValue() * 2);
            }
        }
    }

    public function start(): void
    {
        parent::start();
    }
}