<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\CurrencyUtils;
use pocketmine\event\player\PlayerDeathEvent;

final class BountyHunter extends Perk
{
    final public function start(LegacyPlayer $player, LegacyPlayer $victim): void
    {
        $player->getCurrencyProvider()->add(CurrencyUtils::GOLD, $victim->getPlayerProperties()->getNestedProperties("stats.prime") ?? 0);
    }

    final public function onEvent(): string
    {
        return PlayerDeathEvent::class;
    }
}