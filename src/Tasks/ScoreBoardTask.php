<?php

namespace Legacy\ThePit\Tasks;

use Legacy\ThePit\Managers\EventsManager;
use Legacy\ThePit\Managers\ScoreBoardManager;
use pocketmine\scheduler\Task;

final class ScoreBoardTask extends Task
{
    public function onRun(): void
    {
        foreach (EventsManager::getEvents() as $event){
            match ($event?->getCurrentEvent() ?? EventsManager::TYPE_NONE){
                EventsManager::TYPE_NONE => ScoreBoardManager::updateScoreboard(null, EventsManager::TYPE_NONE),
                EventsManager::TYPE_DEATHMATCH => ScoreBoardManager::updateScoreboard(ScoreBoardManager::getScoreboards()["deathmatch"], EventsManager::TYPE_DEATHMATCH),
                EventsManager::TYPE_RAFFLE => ScoreBoardManager::updateScoreboard(ScoreBoardManager::getScoreboards()["raffle"], EventsManager::TYPE_RAFFLE),
                EventsManager::TYPE_SPIRE => ScoreBoardManager::updateScoreboard(ScoreBoardManager::getScoreboards()["spire"], EventsManager::TYPE_SPIRE),
            };
        }
    }
}