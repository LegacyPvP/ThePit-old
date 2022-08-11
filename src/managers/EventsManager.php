<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\events\Event;

final class EventsManager extends Managers
{
    private ?Event $event = null;

    public function load(): void
    {
        Event::setup();
    }

    public function getCurrentEvent(): ?Event
    {
        return $this->event ?? Event::NONE();
    }

}