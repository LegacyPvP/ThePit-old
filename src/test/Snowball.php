<?php

namespace Legacy\ThePit\test;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\player\Player;

final class Snowball extends \pocketmine\item\Snowball
{
    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new SnowballProjectile($location, $thrower, $this->getNamedTag(), [$thrower]);
    }

}