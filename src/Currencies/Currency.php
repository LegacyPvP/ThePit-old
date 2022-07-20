<?php

namespace Legacy\ThePit\Currencies;

use Legacy\ThePit\Player\LegacyPlayer;

abstract class Currency
{
    public function __construct(private string $name)
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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