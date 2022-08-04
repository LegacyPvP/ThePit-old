<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerDeathEvent;

final class Flash extends Perk
{
    final public function start(LegacyPlayer $player){
        $amplifier = 1;
        foreach ($player->getEffects()->all() as $effectInstance){
            if($effectInstance->getType()::class !== VanillaEffects::SPEED()::class) continue;
            if(($effectInstance->getEffectLevel() + $amplifier) > 4) $amplifier = 4;
            else $amplifier += $effectInstance->getEffectLevel();
            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 15*20, $amplifier, false));
        }
    }

    public function onEvent(): string
    {
        return PlayerDeathEvent::class;
    }
}