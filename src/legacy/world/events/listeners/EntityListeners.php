<?php

namespace legacy\world\events\listeners;

use legacy\world\Main;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;

final class EntityListeners implements Listener
{
    private Main $plugin;


    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    private function getPlugin(): Main
    {
        return $this->plugin;
    }

    public function onEntityExplode(EntityExplodeEvent $event): void
    {
        $api = $this->getPlugin()->getApi();
        if ($api->isInArea($event->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($event->getPosition());
            if (!$flags['tnt']) $event->cancel();
        }
    }
}