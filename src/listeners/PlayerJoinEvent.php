<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent as ClassEvent;

final class PlayerJoinEvent implements Listener
{
    final public function onEvent(ClassEvent $event): void
    {
        $event->setJoinMessage("");
        $player = $event->getPlayer();
        if ($player instanceof LegacyPlayer) {
            $player->init();
            $player::setInCache("initialKnockbackMotion", false);
            $player::setInCache("shouldCancelKBMotion", false);
            $player::setInCache("lastAttackedActorTime", 0);
            $player->teleport($player->getWorld()->getSpawnLocation());
            $grade = Managers::RANKS()->get($player->getPlayerProperties()->getNestedProperties("infos.rank"));
            foreach (($grade?->getPermissions() ?? []) as $permission) {
                $player->setBasePermission($permission, true);
            }
            $packet = Managers::CUSTOMITEMS()->getPacket();
            if (!is_null($packet)) $player->getNetworkSession()->sendDataPacket($packet);
            $player->setStuff();

            foreach ($player->getServer()->getOnlinePlayers() as $_player) {
                if ($_player instanceof LegacyPlayer) {
                    if ($_player->getPlayerProperties()->getNestedProperties("settings.show-join-message")) {
                        $_player->getLanguage()->getMessage("messages.player-join", ["{player}" => $player->getName()], ServerUtils::PREFIX_4)->sendPopup($_player);
                    }
                }
            }
        }
    }
}