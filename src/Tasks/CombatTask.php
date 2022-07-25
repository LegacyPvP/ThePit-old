<?php

namespace Legacy\ThePit\tasks;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\scheduler\Task;

final class CombatTask extends Task
{

    private LegacyPlayer $player;
    private int $time;

    public function __construct(LegacyPlayer $player)
    {
        $this->player = $player;
    }

    public function onRun(): void
    {
        if ($this->player->getInCache("combat", false)) {
            $this->resetTime();
        } else {
            if ($this->time >= 0) {
                $this->player->getLanguage()->getMessage("messages.combat.time", ["{time}" => $this->time])->send($this->player);
                $this->time--;
            } else {
                $this->player->getLanguage()->getMessage("messages.combat.timeout")->send($this->player);
                $this->player->setInCache("combat_players", []);
                self::getHandler()->cancel();
            }
        }
    }

    public function resetTime()
    {
        $this->time = 15;
    }
}