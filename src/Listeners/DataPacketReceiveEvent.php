<?php

namespace Legacy\ThePit\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent as ClassEvent;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;

final class DataPacketReceiveEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        if($event->getPacket() instanceof AnimatePacket){
            $event->getOrigin()->getPlayer()->getServer()->broadcastPackets($event->getOrigin()->getPlayer()->getViewers(), [$event->getPacket()]);
        }
    }
}