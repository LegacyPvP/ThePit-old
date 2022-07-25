<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\Listeners\DataPacketReceiveEvent;
use Legacy\ThePit\Listeners\DataPacketSendEvent;
use Legacy\ThePit\Listeners\EntityDamageByEntityEvent;
use Legacy\ThePit\Listeners\EntityDamageEvent;
use Legacy\ThePit\Listeners\EntityPickupItemEvent;
use Legacy\ThePit\Listeners\PlayerChatEvent;
use Legacy\ThePit\Listeners\PlayerCreationEvent;
use Legacy\ThePit\Listeners\PlayerDropItemEvent;
use Legacy\ThePit\Listeners\PlayerItemUseEvent;
use Legacy\ThePit\Listeners\PlayerJoinEvent;
use pocketmine\Server;

final class ListenersManager extends Managers
{
    #[Pure] public function getAll(): array
    {
        return [
            new PlayerCreationEvent(),
            new DataPacketReceiveEvent(),
            new PlayerChatEvent(),
            new PlayerJoinEvent(),
            new DataPacketSendEvent(),
            new EntityDamageByEntityEvent(),
            new PlayerItemUseEvent(),
            new EntityDamageEvent(),
            new PlayerDropItemEvent(),
            new EntityPickupItemEvent(),
        ];
    }

    public function init(): void
    {
        foreach ($this->getAll() as $event) {
            Server::getInstance()->getPluginManager()->registerEvents($event, Core::getInstance());
            Core::getInstance()->getLogger()->notice("[LISTENERS] Listener: " . $event::class . " Loaded");
        }
    }

    public function get(string $name): ?object
    {
        return null;
    }

}