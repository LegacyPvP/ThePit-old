<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent;

final class Vampire extends Perk
{
    public const SOURCE_SWORD = 1;
    public const SOURCE_BOW = 2;

    final public function start(LegacyPlayer $player, int $source = self::SOURCE_SWORD): void
    {
        if (rand(1, 3) === 1) {
            $player->setHealth($player->getHealth() + match ($source) {
                    self::SOURCE_SWORD => 0.5,
                    self::SOURCE_BOW => 1,
                    default => 0
                });
        }

    }

    final public function onEvent(): string
    {
        return EntityDamageByEntityEvent::class;
    }
}