<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Objects\Sound;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\CurrencyUtils;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityItemPickupEvent as ClassEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;

final class EntityPickupItemEvent implements Listener {

    public function onEvent(ClassEvent $event): void {
        $item = $event->getItem();
        $entity = $event->getEntity();
        $item_entity = $event->getOrigin();
        if($item->getId() == ItemIds::GOLD_INGOT and $entity instanceof LegacyPlayer){
            $count = mt_rand(1, 3);
            $entity->getCurrencyProvider()->add(CurrencyUtils::GOLD, $count);
            $sound = new Sound("random.orb", 1);
            $sound->play($entity);
            if($item_entity instanceof ItemEntity){
                $item_entity->flagForDespawn();
                $event->cancel();
            }
        }
    }
}
