<?php

namespace Legacy\ThePit\traits;

use pocketmine\event\Event;

trait PerkTrait
{
    public function canStart(Event $event){
        return true;
    }

}