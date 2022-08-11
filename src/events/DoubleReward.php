<?php

namespace Legacy\ThePit\events;

use Legacy\ThePit\listeners\events\PlayerCurrencyChangeEvent;
use pocketmine\event\Event;

final class DoubleReward extends MinorEvent
{
    public function handle(Event $event): void {
        if($event instanceof PlayerCurrencyChangeEvent){

        }
    }

    public function start(): void
    {
        parent::start();
    }
}