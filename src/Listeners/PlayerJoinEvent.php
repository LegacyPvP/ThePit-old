<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\CustomItemManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\color\Color;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent as ClassEvent;
use pocketmine\item\VanillaItems;

final class PlayerJoinEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        if(($player = $event->getPlayer()) instanceof LegacyPlayer){
            $grade = RanksManager::parseRank($player->getPlayerProperties()->getNestedProperties("infos.rank"));
            foreach ($grade->getPermissions() as $permission){
                $player->setBasePermission($permission, true);
            }
            $packet = CustomItemManager::getPacket();
            if (!is_null($packet)) $player->getNetworkSession()->sendDataPacket($packet);
        }
    }

}