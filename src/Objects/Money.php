<?php

namespace Legacy\ThePit\Objects;

use Legacy\ThePit\Player\LegacyPlayer;

final class Money
{
    public function __construct(private string $name, private bool $byVoting = false)
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isByVoting(): bool
    {
        return $this->byVoting;
    }

    public function add(LegacyPlayer $player, int $amount): void
    {
        $player->getPlayerProperties()->setNestedProperties("stats.$this->name", $player->getPlayerProperties()->getNestedProperties("stats.$this->name") + $amount);
    }

    public function remove(LegacyPlayer $player, int $amount): void
    {
        $player->getPlayerProperties()->setNestedProperties("stats.$this->name", $player->getPlayerProperties()->getNestedProperties("stats.$this->name") - $amount);
    }

    public function get(LegacyPlayer $player): int
    {
        return $player->getPlayerProperties()->getNestedProperties("stats.$this->name") ?? 0;
    }

    public function set(LegacyPlayer $player, int $amount): void
    {
        $player->getPlayerProperties()->setNestedProperties("stats.$this->name", $amount);
    }

    public function has(LegacyPlayer $player, int $amount): bool
    {
        return $player->getPlayerProperties()->getNestedProperties("stats.$this->name") >= $amount;
    }
}