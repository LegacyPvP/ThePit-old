<?php

namespace Legacy\ThePit\managers;

final class KnockBackManager extends Managers
{
    public function getHorizontal(): float
    {
        return Managers::DATA()->get("config")->getNested("knockback.horizontal", 0.40);
    }

    public function getVertical(): float
    {
        return Managers::DATA()->get("config")->getNested("knockback.vertical", 0.40);
    }

    public function getAttackCooldown(): int
    {
        return Managers::DATA()->get("config")->getNested("knockback.attack_cooldown", 10);
    }

}