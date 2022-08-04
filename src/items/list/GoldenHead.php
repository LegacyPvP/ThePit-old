<?php

namespace Legacy\ThePit\items\list;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\item\GoldenAppleEnchanted;

final class GoldenHead extends GoldenAppleEnchanted
{
    public function onConsume(Living $consumer): void
    {
        $consumer->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 5*20, 2, false));
        $consumer->getEffects()->add(new EffectInstance(VanillaEffects::ABSORPTION(), 120*20, 1, false));
    }
}