<?php

namespace Legacy\ThePit\events;

use pocketmine\Server;

abstract class MajorEvent extends Event
{
    final public function canStart(): bool {
        return count(Server::getInstance()->getOnlinePlayers()) >= 50;
    }

    final public function getType(): int
    {
        return parent::TYPE_MAJOR;
    }
}