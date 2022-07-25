<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\items\list\Spell;
use Legacy\ThePit\utils\SpellUtils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent as ClassEvent;
use pocketmine\item\Book;

final class PlayerItemUseEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        if ($event->getItem() instanceof Book) {
            Spell::openSpell($event->getPlayer());
        }

        if ($event->getItem()->getCustomName() == SpellUtils::SPELL_HEALTH_NAME) {
            if (!$event->getPlayer()->getHealth() == $event->getPlayer()->getMaxHealth()) {
                $event->getPlayer()->setHealth($event->getPlayer()->getHealth() + 4);
                $event->getPlayer()->sendPopup($event->getPlayer()->getLanguage()->getMessage("messages.interactions.spells.health.success"));
                if ($event->getPlayer()->getInventory()->contains($event->getItem())) {
                    $event->getPlayer()->getInventory()->removeItem($event->getItem());
                }
            } else {
                $event->getPlayer()->sendPopup($event->getPlayer()->getLanguage()->getMessage("messages.interactions.spells.health.full"));
            }
        } elseif ($event->getItem()->getCustomName() == SpellUtils::SPELL_SPEED_NAME) {
            $event->getPlayer()->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 100, 3));
            $event->getPlayer()->sendPopup($event->getPlayer()->getLanguage()->getMessage("messages.interactions.spells.speed.success"));
            if ($event->getPlayer()->getInventory()->contains($event->getItem())) {
                $event->getPlayer()->getInventory()->removeItem($event->getItem());
            }
        }
    }
}