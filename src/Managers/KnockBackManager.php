<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;

abstract class KnockBackManager
{
    public static function getHorizontal(): float{
        return Core::getInstance()->getConfig()->getNested("knockback.horizontal", 0.40);
    }

    public static function getVertical(): float{
        return Core::getInstance()->getConfig()->getNested("knockback.vertical", 0.40);
    }

    public static function getAttackCooldown(): int {
        return Core::getInstance()->getConfig()->getNested("knockback.attack_cooldown", 10);
    }

}