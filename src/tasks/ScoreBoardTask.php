<?php

namespace Legacy\ThePit\tasks;

use Legacy\ThePit\events\Events;
use Legacy\ThePit\managers\Managers;
use pocketmine\scheduler\Task;

final class ScoreBoardTask extends Task
{
    public function onRun(): void
    {
        match (Managers::EVENTS()->getCurrentEvent()->getName()){
            Events::DEATHMATCH()->getName() => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("deathmatch"), Events::DEATHMATCH()->getName()),
            Events::RAFFLE()->getName() => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("raffle"), Events::RAFFLE()->getName()),
            Events::SPIRE()->getName() => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("spire"), Events::SPIRE()->getName()),
            default => Managers::SCOREBOARDS()->updateScoreboard(null, Events::NONE()->getName()),
        };
    }
}