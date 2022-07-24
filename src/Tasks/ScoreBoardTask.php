<?php

namespace Legacy\ThePit\Tasks;

use Legacy\ThePit\Managers\Managers;
use pocketmine\scheduler\Task;

final class ScoreBoardTask extends Task
{
    public function onRun(): void
    {
        match (Managers::EVENTS()->getCurrentEvent() ?? Managers::EVENTS()::TYPE_NONE) {
            Managers::EVENTS()::TYPE_NONE => Managers::SCOREBOARDS()->updateScoreboard(null, Managers::EVENTS()::TYPE_NONE),
            Managers::EVENTS()::TYPE_DEATHMATCH => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("deathmatch"), Managers::EVENTS()::TYPE_DEATHMATCH),
            Managers::EVENTS()::TYPE_RAFFLE => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("raffle"), Managers::EVENTS()::TYPE_RAFFLE),
            Managers::EVENTS()::TYPE_SPIRE => Managers::SCOREBOARDS()->updateScoreboard(Managers::SCOREBOARDS()->get("spire"), Managers::EVENTS()::TYPE_SPIRE),
        };
    }
}