<?php

namespace Legacy\ThePit\listeners\events;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;

final class PlayerCurrencyChangeEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(LegacyPlayer $player, private string $currency, private int $value)
    {
        $this->player = $player;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(int $value): void {
        $this->value += $value;
    }

    public function remove(int $value): void {
        $this->value -= $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getPlayer(): LegacyPlayer
    {
        return $this->player;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }
}