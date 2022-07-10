<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\GradesManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent as ClassEvent;

final class PlayerJoinEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        if(($player = $event->getPlayer()) instanceof LegacyPlayer){
            $grade = GradesManager::parseGrade($player->getPlayerProperties()->getNestedProperties("infos.grade"));
            foreach ($grade->getPermissions() as $permission){
                $player->setBasePermission($permission, true);
            }
        }
    }

}