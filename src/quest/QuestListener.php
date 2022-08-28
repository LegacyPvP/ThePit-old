<?php

namespace Legacy\ThePit\quest;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerEvent;

/**
 * Yanoox:
 * Cette class sert à executer les callable instauré lors de la création des étapes des quêtes.
 * J'ai voulu utilisé PlayerEvent pour éviter de copier/coller le code dans chaque listener (question pratique est beau)
 * pour tout autre idée je suis preneur, si je ne répond pas dans les 30 minutes, veuillez me mp. Merci
 */
class QuestListener implements Listener
{
    /**
     * Yanoox :
     * Vu que PlayerEvent est une class abstraite, j'ai essayé de @allowHandle mais l'erreur continue d'apparaître
     * si vous avez des idées je suis preneur, ou alors il va falloir execute le callback sur chaque class des listeners
     * @param PlayerEvent $event
     * @return void
     */
    public function onEvent(PlayerEvent $event)
    {
        $player = $event->getPlayer();
        if (!$player instanceof LegacyPlayer) return;
        foreach ($player->getQuestProvider()->getAll() as $quest) {
            $quest->getCurrentStep()->executeCallBack($event->getEventName(), $event);
        }
    }
}