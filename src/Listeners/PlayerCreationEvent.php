<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent as ClassEvent;

final class PlayerCreationEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $event->setPlayerClass(LegacyPlayer::class);
    }
}