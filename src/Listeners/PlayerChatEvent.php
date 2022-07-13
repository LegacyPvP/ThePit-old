<?php /** @noinspection PhpMissingBreakStatementInspection */

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\MuteManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent as ClassEvent;

final class PlayerChatEvent implements Listener
{
    /**
     * @param ClassEvent $event
     * @priority LOWEST
     */
    public function onEvent(ClassEvent $event){
        $player = $event->getPlayer();
        if($player instanceof LegacyPlayer){
            $event->setFormat($player->getGrade()->getFormat([
                "{player}" => $player->getName(),
                "{chat}" => $event->getMessage(),
            ]));
            $event->setMessage("");
            switch (true){
                case MuteManager::isMuted($player):
                    $player->getLanguage()->getMessage("messages.mute.muted", ["{player}" => MuteManager::getStaff($player),
                        "{date}" => date("d/m/Y H:i:s", MuteManager::getTime($player)), "{reason}" => MuteManager::getReason($player)], ServerUtils::PREFIX_3)->send($player);
                case ServerUtils::isGlobalMute():
                    $event->cancel();
            }
        }
    }
}