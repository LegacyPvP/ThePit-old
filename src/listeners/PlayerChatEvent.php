<?php /** @noinspection PhpMissingBreakStatementInspection */

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent as ClassEvent;

final class PlayerChatEvent implements Listener
{
    /**
     * @param ClassEvent $event
     * @priority LOWEST
     */
    public function onEvent(ClassEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof LegacyPlayer) {
            $event->setFormat($player->getRank()->getFormat([
                "{prestige}" => $player->getPlayerProperties()->getNestedProperties("stats.prestige"),
                "{player}" => $player->getName(),
                "{chat}" => $event->getMessage(),
            ]));
            $event->setMessage("");
            switch (true) {
                case Managers::MUTES()->isMuted($player):
                    $player->getLanguage()->getMessage("messages.mute.muted", ["{player}" => Managers::MUTES()->getStaff($player),
                        "{date}" => date("d/m/Y H:i:s", Managers::MUTES()->getTime($player)), "{reason}" => Managers::MUTES()->getReason($player)], ServerUtils::PREFIX_3)->send($player);
                case ServerUtils::isGlobalMute():
                    $event->cancel();
            }
        }
    }
}