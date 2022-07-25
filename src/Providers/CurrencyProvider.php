<?php

namespace Legacy\ThePit\Providers;

use Legacy\ThePit\Managers\Managers;
use Legacy\ThePit\Player\LegacyPlayer;

final class CurrencyProvider
{
    public function __construct(private LegacyPlayer $player)
    {
    }

    public function get(string $currency): int
    {
        return Managers::CURRENCIES()->get($currency)->get($this->player);
    }

    public function has(string $currency, int $amount): bool
    {
        return Managers::CURRENCIES()->get($currency)->has($this->player, $amount);
    }

    public function set(string $currency, int $amount): void
    {
        Managers::CURRENCIES()->get($currency)->set($this->player, $amount);
    }

    public function add(string $currency, int $amount): void
    {
        Managers::CURRENCIES()->get($currency)->add($this->player, $amount);
    }

    public function remove(string $currency, int $amount): void
    {
        Managers::CURRENCIES()->get($currency)->remove($this->player, $amount);
    }
}