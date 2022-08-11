<?php

namespace Legacy\ThePit\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent as ClassEvent;

final class PlayerDropItemEvent implements Listener
{
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    final public function onEvent(ClassEvent $event): void
    {
        match ($event->getItem()->getId()) {
            298, 299, 300, 301 => match ($event->getItem()->getCustomColor()) {
                null => $event->cancel(),
                default => null
            },
            default => $event->cancel()
        };
    }
}