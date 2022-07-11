<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Managers\CustomItemManager;
use pocketmine\event\server\DataPacketSendEvent as ClassEvent;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;

final class DataPacketSendEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $packets = $event->getPackets();
        foreach ($packets as $packet){
            if($packet instanceof ResourcePacksInfoPacket or $packet instanceof ResourcePackStackPacket or
                $packet instanceof ResourcePackClientResponsePacket or $packet instanceof ResourcePackDataInfoPacket or $packet instanceof ResourcePackChunkDataPacket or $packet instanceof ResourcePackChunkRequestPacket){
                var_dump($packet);
            }
        }
        $experimentsOverridden = [
            CustomItemManager::DATA_DRIVEN_ITEMS => true,
            CustomItemManager::EXPERIMENTAL_MOLANG_FEATURES => true,
            CustomItemManager::GAMETEST => true,
            CustomItemManager::SCRIPTING => true,
            CustomItemManager::UPCOMING_CREATOR_FEATURES => true
        ];

        foreach ($packets as $packet) {
            if ($packet instanceof StartGamePacket) {


                $packet->levelSettings->experiments = new Experiments([
                    "data_driven_items" => true
                ], true);

                $experiments = $packet->levelSettings->experiments;
                /**
                 * @noinspection PhpExpressionResultUnusedInspection
                 * HACK : Modifying properties using public constructors
                 */
                $experiments->__construct(
                    array_merge($experiments->getExperiments(), $experimentsOverridden),
                    $experiments->hasPreviouslyUsedExperiments()
                );
            } elseif ($packet instanceof ResourcePackStackPacket) {

                $packet->experiments = new Experiments([
                    "data_driven_items" => true
                ], true);

                $experiments = $packet->experiments;
                /**
                 * @noinspection PhpExpressionResultUnusedInspection
                 * HACK : Modifying properties using public constructors
                 */
                $experiments->__construct(
                    array_merge($experiments->getExperiments(), $experimentsOverridden),
                    $experiments->hasPreviouslyUsedExperiments()
                );
            }
        }
    }
}