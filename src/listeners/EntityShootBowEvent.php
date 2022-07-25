<?php

namespace Legacy\ThePit\listeners;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityShootBowEvent as ClassEvent;

final class EntityShootBowEvent implements Listener {

    #[Pure] public function onEvent(ClassEvent $event): void {
        $player = $event->getEntity();
        if($player instanceof LegacyPlayer){

        }
    }
}
