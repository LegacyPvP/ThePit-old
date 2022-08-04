<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitEntityEvent as ClassEvent;

final class ProjectileHitEntityEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $player = $event->getEntity()->getOwningEntity();
        $projectile = $event->getEntity();
        if($player instanceof LegacyPlayer and $projectile instanceof Arrow){
            $player->getPerksProvider()->onEvent($event::class);
        }
    }

}