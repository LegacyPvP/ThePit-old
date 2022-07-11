<?php

namespace Legacy\ThePit\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent as ClassEvent;

final class EntityDamageEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        match ($event->getCause()){
            $event::CAUSE_FALL => $event->cancel(),
            default => null
        };
    }
}