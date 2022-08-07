<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent;

final class Vampire extends Perk
{
    final public function start(EntityDamageByEntityEvent $event): void
    {
        if(!($player = $event->getDamager()) instanceof LegacyPlayer) return;
        if (rand(1, 3) === 1) {
            $player->setHealth($player->getHealth() + match ($event->getCause()) {
                    $event::CAUSE_ENTITY_ATTACK => 0.5,
                    $event::CAUSE_PROJECTILE => 1,
                    default => 0
                });
        }

    }

    final public function canStart(EntityDamageByEntityEvent $event){
        return true;
    }

    final public function onEvent(): string
    {
        return EntityDamageByEntityEvent::class;
    }
}