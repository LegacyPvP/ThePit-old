<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\CurrencyUtils;
use pocketmine\event\entity\EntityItemPickupEvent;

final class Nabbit extends Perk
{
    public function start(LegacyPlayer $player, int $gold): void{
        $player->getCurrencyProvider()->add(CurrencyUtils::GOLD, $gold);
    }

    public function onEvent(): string
    {
        return EntityItemPickupEvent::class;
    }
}