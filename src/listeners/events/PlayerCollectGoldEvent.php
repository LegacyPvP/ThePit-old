<?php

namespace Legacy\ThePit\listeners\events;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

final class PlayerCollectGoldEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private int $gold)
    {
        $this->player = $player;
    }

    /**
     * @return int
     */
    public function getGold(): int
    {
        return $this->gold;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }
}