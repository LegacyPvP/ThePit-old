<?php

namespace Legacy\ThePit\events;

use pocketmine\Server;

abstract class MajorEvents extends Events
{
    final public function canStart(): bool {
        return count(Server::getInstance()->getOnlinePlayers()) >= 50;
    }

    final public function getType(): int
    {
        return parent::TYPE_MAJOR;
    }
}