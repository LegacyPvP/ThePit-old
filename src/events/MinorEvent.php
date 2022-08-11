<?php

namespace Legacy\ThePit\events;

use pocketmine\Server;

abstract class MinorEvent extends Event
{
    final public function canStart(){
        return true;
    }

    final public function getType(): int
    {
        return parent::TYPE_MINOR;
    }

    final public function stop(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $player->getLanguage()->getMessage('messages.event.stop', [
                '{event}' => $this->getName()
            ])->send($player);
        }
    }
}