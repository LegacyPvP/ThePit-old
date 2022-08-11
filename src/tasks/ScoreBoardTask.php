<?php

namespace Legacy\ThePit\tasks;

use Legacy\ThePit\events\Event;
use Legacy\ThePit\managers\Managers;
use pocketmine\scheduler\Task;

final class ScoreBoardTask extends Task
{
    public function onRun(): void
    {
        match (Managers::EVENTS()->getCurrentEvent()->getName()){
            Event::DEATHMATCH()->getName() => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("deathmatch"), Event::DEATHMATCH()->getName()),
            Event::RAFFLE()->getName() => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("raffle"), Event::RAFFLE()->getName()),
            Event::SPIRE()->getName() => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("spire"), Event::SPIRE()->getName()),
            default => Managers::SCOREBOARDS()->updateScoreboard(null, Event::NONE()->getName()),
        };
    }
}