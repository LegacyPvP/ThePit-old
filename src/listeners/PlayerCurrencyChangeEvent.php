<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\managers\Managers;
use pocketmine\event\Listener;
use Legacy\ThePit\listeners\events\PlayerCurrencyChangeEvent as ClassEvent;

final class PlayerCurrencyChangeEvent implements Listener
{
    final public function onEvent(ClassEvent $event): void
    {
        Managers::EVENTS()->getCurrentEvent()->handle($event);
    }
}