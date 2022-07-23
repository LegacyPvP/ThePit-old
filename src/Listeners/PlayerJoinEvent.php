<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\CustomItemManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent as ClassEvent;

final class PlayerJoinEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $event->setJoinMessage("");
        if(($player = $event->getPlayer()) instanceof LegacyPlayer){
            $grade = RanksManager::parseRank($player->getPlayerProperties()->getNestedProperties("infos.rank"));
            foreach (($grade?->getPermissions() ?? []) as $permission){
                $player->setBasePermission($permission, true);
            }
            $packet = CustomItemManager::getPacket();
            if (!is_null($packet)) $player->getNetworkSession()->sendDataPacket($packet);
        }

        foreach($player->getServer()->getOnlinePlayers() as $_player){
            if($_player instanceof LegacyPlayer){
                if($_player->getPlayerProperties()->getNestedProperties("settings.show-join-message")){
                    $_player->getLanguage()->getMessage("messages.player-join", ["{player}" => $player->getName()], ServerUtils::PREFIX_4)->sendPopup($_player);
                }
            }
        }
    }

}