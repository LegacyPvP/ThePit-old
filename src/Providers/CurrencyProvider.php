<?php

namespace Legacy\ThePit\Providers;

use Legacy\ThePit\Managers\CurrenciesManager;
use Legacy\ThePit\Player\LegacyPlayer;

final class CurrencyProvider
{
    public function __construct(private LegacyPlayer $player)
    {
    }

    public function get(string $currency): int
    {
        return CurrenciesManager::getCurrency($currency)->get($this->player);
    }

    public function has(string $currency, int $amount): bool
    {
        return CurrenciesManager::getCurrency($currency)->has($this->player, $amount);
    }

    public function set(string $currency, int $amount): void
    {
        CurrenciesManager::getCurrency($currency)->set($this->player, $amount);
    }

    public function add(string $currency, int $amount): void
    {
        CurrenciesManager::getCurrency($currency)->add($this->player, $amount);
    }

    public function remove(string $currency, int $amount): void
    {
        CurrenciesManager::getCurrency($currency)->remove($this->player, $amount);
    }
}