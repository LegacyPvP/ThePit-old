<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;

final class KnockBackManager extends Managers
{
    public function getHorizontal(): float
    {
        return Core::getInstance()->getConfig()->getNested("knockback.horizontal", 0.40);
    }

    public function getVertical(): float
    {
        return Core::getInstance()->getConfig()->getNested("knockback.vertical", 0.40);
    }

    public function getAttackCooldown(): int
    {
        return Core::getInstance()->getConfig()->getNested("knockback.attack_cooldown", 10);
    }

}