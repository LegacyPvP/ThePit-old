<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\managers\Managers;
use pocketmine\event\server\DataPacketSendEvent as ClassEvent;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;

final class DataPacketSendEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $packets = $event->getPackets();
        $experimentsOverridden = [
            Managers::CUSTOMITEMS()::DATA_DRIVEN_ITEMS => true,
            Managers::CUSTOMITEMS()::EXPERIMENTAL_MOLANG_FEATURES => true,
            Managers::CUSTOMITEMS()::GAMETEST => true,
            Managers::CUSTOMITEMS()::SCRIPTING => true,
            Managers::CUSTOMITEMS()::UPCOMING_CREATOR_FEATURES => true
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