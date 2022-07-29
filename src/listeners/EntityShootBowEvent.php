<?php

namespace Legacy\ThePit\listeners;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\test\Arrow;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityShootBowEvent as ClassEvent;

final class EntityShootBowEvent implements Listener {

    #[Pure] public function onEvent(ClassEvent $event): void {
        $player = $event->getEntity();
        if($player instanceof LegacyPlayer){
            $location = $player->getLocation();
            $world = $location->getWorld();
            $diff = $player->getItemUseDuration();
            $p = $diff / 20;
            $baseForce = min((($p ** 2) + $p * 2) / 3, 1);
            $arrow = new Arrow(Location::fromObject($player->getEyePos(), $world, ($location->yaw > 180 ? 360 : 0) - $location->yaw, -$location->pitch), $player, $baseForce >= 1, null, PracticeUtil::getViewersForPosition($player));
            /** @var Arrow $projectile */
            $projectile = $event->getProjectile();
            $arrow->setPunchKnockback($projectile->getPunchKnockback());
            $event->setProjectile($arrow);
            return;
        }
        $event->cancel();
    }
}
