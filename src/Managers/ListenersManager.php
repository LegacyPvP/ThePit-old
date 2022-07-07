<?php
namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Listeners\DataPacketReceiveEvent;
use Legacy\ThePit\Listeners\PlayerCreationEvent;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayMode;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\network\mcpe\protocol\types\InteractionMode;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\plugin\Plugin;

abstract class ListenersManager
{
    public static function getListeners(): array {
        return [
            new PlayerCreationEvent(),
            new DataPacketReceiveEvent()
        ];
    }

    public static function initListeners(Plugin $plugin): void {
        foreach (self::getListeners() as $event){
            $plugin->getServer()->getPluginManager()->registerEvents($event, $plugin);
            Core::getInstance()->getLogger()->notice("[LISTENERS] Listener: ".$event::class." Loaded");
        }
    }

}