<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent as ClassEvent;

final class PlayerDeathEvent implements Listener
{
    final public function onEvent(ClassEvent $event): void
    {
        $event->setDeathMessage("");
    }
}