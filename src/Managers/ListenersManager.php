<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\Listeners\DataPacketReceiveEvent;
use Legacy\ThePit\Listeners\DataPacketSendEvent;
use Legacy\ThePit\Listeners\EntityDamageByEntityEvent;
use Legacy\ThePit\Listeners\EntityDamageEvent;
use Legacy\ThePit\Listeners\EntityShootBowEvent;
use Legacy\ThePit\Listeners\PlayerChatEvent;
use Legacy\ThePit\Listeners\PlayerCreationEvent;
use Legacy\ThePit\Listeners\PlayerDropItemEvent;
use Legacy\ThePit\Listeners\PlayerItemUseEvent;
use Legacy\ThePit\Listeners\PlayerJoinEvent;
use pocketmine\plugin\Plugin;

abstract class ListenersManager
{
    #[Pure] public static function getListeners(): array
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
            new EntityShootBowEvent(),
        ];
    }

    public static function initListeners(Plugin $plugin): void
    {
        foreach (self::getListeners() as $event) {
            $plugin->getServer()->getPluginManager()->registerEvents($event, $plugin);
            Core::getInstance()->getLogger()->notice("[LISTENERS] Listener: " . $event::class . " Loaded");
        }
    }

}