<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerDeathEvent;

final class SerialKiller extends Perk
{
    public function start(LegacyPlayer $player): void
    {
        $amplifier = 1;
        foreach ($player->getEffects()->all() as $effectInstance){
            if($effectInstance->getType()::class !== VanillaEffects::STRENGTH()::class) continue;
            if(($effectInstance->getEffectLevel() + $amplifier) > 3) $amplifier = 3;
            else $amplifier += $effectInstance->getEffectLevel();
            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 10*20, $amplifier, false));
        }
    }

    public function onEvent(): string
    {
        return PlayerDeathEvent::class;
    }
}