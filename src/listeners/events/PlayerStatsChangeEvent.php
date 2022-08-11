<?php

namespace Legacy\ThePit\listeners\events;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;

final class PlayerStatsChangeEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(LegacyPlayer $player, private string $type, private int $value)
    {
        $this->player = $player;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
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