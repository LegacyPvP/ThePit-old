<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\events\PlayerCollectGoldEvent;
use Legacy\ThePit\objects\Sound;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\CurrencyUtils;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityItemPickupEvent as ClassEvent;
use pocketmine\item\ItemIds;

final class EntityItemPickupEvent implements Listener {

    public function onEvent(ClassEvent $event): void {
        $item = $event->getItem();
        $entity = $event->getEntity();
        $item_entity = $event->getOrigin();
        if($item->getId() == ItemIds::GOLD_INGOT and $entity instanceof LegacyPlayer){
            $count = rand(1, 4);
            $ev = new PlayerCollectGoldEvent($entity, $count);
            $ev->call();
            if($ev->isCancelled()) return;
            $entity->getPerksProvider()->onEvent($ev);
            $entity->getCurrencyProvider()->add(CurrencyUtils::GOLD, $count);
            $sound = new Sound("random.orb", 1);
            $sound->play($entity);
            $entity->sendPopup("§6- §e+$count §6-");
            if($item_entity instanceof ItemEntity){
                $item_entity->flagForDespawn();
                $event->cancel();
            }
        }
    }
}
