<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent as ClassEvent;

final class PlayerJoinEvent implements Listener
{
    public static array $cachedData = ["lastAttackedActorTime" => 0];

    public function onEvent(ClassEvent $event): void
    {
        $event->setJoinMessage("");
        if (($player = $event->getPlayer()) instanceof LegacyPlayer) {
            self::$cachedData[$event->getPlayer()->getName()] = ["initialKnockbackMotion" => false, "shouldCancelKBMotion" => false, "lastAttackedActorTime" => 0];
            $player->teleport($player->getWorld()->getSpawnLocation());
            if ($player instanceof LegacyPlayer) {
                $grade = Managers::RANKS()->get($player->getPlayerProperties()->getNestedProperties("infos.rank"));
                foreach (($grade?->getPermissions() ?? []) as $permission) {
                    $player->setBasePermission($permission, true);
                }
                $packet = Managers::CUSTOMITEMS()->getPacket();
                if (!is_null($packet)) $player->getNetworkSession()->sendDataPacket($packet);
                $player->setStuff();
            }

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