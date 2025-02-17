<?php

namespace Legacy\ThePit\managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\listeners\DataPacketReceiveEvent;
use Legacy\ThePit\listeners\DataPacketSendEvent;
use Legacy\ThePit\listeners\EntityDamageByEntityEvent;
use Legacy\ThePit\listeners\EntityDamageEvent;
use Legacy\ThePit\listeners\EntityItemPickupEvent;
use Legacy\ThePit\listeners\PlayerChatEvent;
use Legacy\ThePit\listeners\PlayerCreationEvent;
use Legacy\ThePit\listeners\PlayerCurrencyChangeEvent;
use Legacy\ThePit\listeners\PlayerDeathEvent;
use Legacy\ThePit\listeners\PlayerDropItemEvent;
use Legacy\ThePit\listeners\PlayerInteractEvent;
use Legacy\ThePit\listeners\PlayerItemUseEvent;
use Legacy\ThePit\listeners\PlayerJoinEvent;
use Legacy\ThePit\listeners\PlayerStatsChangeEvent;
use Legacy\ThePit\quest\QuestListener;
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
            new EntityItemPickupEvent(),
            new PlayerDeathEvent(),
            new PlayerCurrencyChangeEvent(),
            new PlayerStatsChangeEvent(),
            new PlayerInteractEvent(),
            //new QuestListener(), //TODO: SOUCIS NON-MAJEUR DETECTE, voir commentaire class
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