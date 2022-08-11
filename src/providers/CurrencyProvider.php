<?php

namespace Legacy\ThePit\providers;

use Legacy\ThePit\listeners\events\PlayerCurrencyChangeEvent;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;

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
        $ev = new PlayerCurrencyChangeEvent($this->player, $currency, $amount);
        $ev->call();
        if($ev->isCancelled()) return;
        Managers::CURRENCIES()->get($currency)->set($this->player, $amount);
    }

    public function add(string $currency, int $amount): void
    {
        $ev = new PlayerCurrencyChangeEvent($this->player, $currency, $this->get($currency) + $amount);
        $ev->call();
        if($ev->isCancelled()) return;
        Managers::CURRENCIES()->get($currency)->add($this->player, $amount);
    }

    public function remove(string $currency, int $amount): void
    {
        $ev = new PlayerCurrencyChangeEvent($this->player, $currency, $this->get($currency) - $amount);
        $ev->call();
        if($ev->isCancelled()) return;
        Managers::CURRENCIES()->get($currency)->remove($this->player, $amount);
    }
}