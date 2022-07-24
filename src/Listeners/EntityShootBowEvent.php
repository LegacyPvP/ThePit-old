<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityShootBowEvent as ClassEvent;

final class EntityShootBowEvent implements Listener {

    public function onEvent(ClassEvent $event): void {
        $player = $event->getEntity();
        if($player instanceof LegacyPlayer){

        }
    }
}
