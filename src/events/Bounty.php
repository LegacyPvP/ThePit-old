<?php

namespace Legacy\ThePit\events;

use Legacy\ThePit\utils\StatsUtils;
use pocketmine\Server;

final class Bounty extends MinorEvent
{
    public function start(): void
    {
        parent::start();
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $player->getStatsProvider()->add(StatsUtils::PRIME, 100);
        }
    }
}