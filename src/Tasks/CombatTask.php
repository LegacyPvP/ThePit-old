<?php

namespace Legacy\ThePit\Tasks;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\scheduler\Task;

final class CombatTask extends Task {

    private LegacyPlayer $player;
    private int $time;

    public function __construct(LegacyPlayer $player)
    {
        $this->player = $player;
    }

    public function onRun(): void
    {
        if($this->player->isInCombat()){
            $this->resetTime();
        }else{
            if($this->time >= 0) {
                $this->player->getLanguage()->getMessage("messages.combat.time", ["{time}" => $this->time])->send($this->player);
                $this->time--;
            }else{
                $this->player->getLanguage()->getMessage("messages.combat.timeout")->send($this->player);
                $this->player->getPlayerProperties()->setNestedProperties("status.combat_players", []);
                self::getHandler()->cancel();
            }
        }
    }

    public function resetTime()
    {
        $this->time = 15;
    }
}