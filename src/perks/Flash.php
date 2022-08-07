<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\listeners\EntityDamageByEntityEvent;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerDeathEvent;

final class Flash extends Perk
{
    final public function start(PlayerDeathEvent $event){
        if(!($cause = $event->getPlayer()->getLastDamageCause()) instanceof EntityDamageByEntityEvent) return;
        $player = $cause->getDamager() instanceof LegacyPlayer ? $cause->getDamager() : null;
        if(!$player) return;
        $amplifier = 1;
        foreach ($player->getEffects()->all() as $effectInstance){
            if($effectInstance->getType()::class !== VanillaEffects::SPEED()::class) continue;
            if(($effectInstance->getEffectLevel() + $amplifier) > 4) $amplifier = 4;
            else $amplifier += $effectInstance->getEffectLevel();
            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 15*20, $amplifier, false));
        }
    }

    final public function canStart(PlayerDeathEvent $event){
        return true;
    }

    public function onEvent(): string
    {
        return PlayerDeathEvent::class;
    }
}