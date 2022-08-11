<?php

namespace Legacy\ThePit\providers;

use Legacy\ThePit\listeners\events\PlayerStatsChangeEvent;
use Legacy\ThePit\player\LegacyPlayer;

final class StatsProvider
{
    public function __construct(private LegacyPlayer $player)
    {
    }

    public function get(string $type): int
    {
        return $this->player->getPlayerProperties()->getNestedProperties("stats.$type") ?? 0;
    }

    public function set(string $type, int $amount): void
    {
        $ev = new PlayerStatsChangeEvent($this->player, $type, $amount);
        $ev->call();
        if($ev->isCancelled()) return;
        $this->player->getPlayerProperties()->setNestedProperties("stats.$type", $amount);
    }

    public function add(string $type, int $amount): void
    {
        $ev = new PlayerStatsChangeEvent($this->player, $type, $this->get($type) + $amount);
        $ev->call();
        if($ev->isCancelled()) return;
        $this->player->getPlayerProperties()->setNestedProperties("stats.$type", $this->get($type) + $amount);
    }

    public function remove(string $type, int $amount): void
    {
        $ev = new PlayerStatsChangeEvent($this->player, $type, $this->get($type) - $amount);
        $ev->call();
        if($ev->isCancelled()) return;
        $this->player->getPlayerProperties()->setNestedProperties("stats.$type", $this->get($type) - $amount);
    }

    public function has(string $type, int $amount): bool
    {
        return $this->get($type) >= $amount;
    }
}