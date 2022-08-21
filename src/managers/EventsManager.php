<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\events\Events;

final class EventsManager extends Managers
{
    private ?Events $event = null;

    public function load(): void
    {
        Events::setup();
    }

    public function getCurrentEvent(): ?Events
    {
        return $this->event ?? Events::NONE();
    }

}