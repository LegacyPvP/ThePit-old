<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Core;
use Legacy\ThePit\Items\List\Nemo;
use Legacy\ThePit\Items\List\Spell;
use Legacy\ThePit\Managers\CooldownManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Tasks\CombatTask;
use Legacy\ThePit\Utils\SpellUtils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent as ClassEvent;
use pocketmine\event\Listener;

final class EntityDamageByEntityEvent implements Listener {

    public function onEvent(ClassEvent $event): void
    {
        $event->setKnockBack(0);
        if($event->getEntity()->getId() !== $event->getDamager()?->getId() && $event->getModifier(\pocketmine\event\entity\EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN) >= 0.0){
            if(($damager = $event->getDamager()) instanceof LegacyPlayer) {
                $vector = $damager->getDirectionVector();
                $item = $event->getDamager()->getInventory()->getItemInHand();
                if ($damager->isImmobile()) {
                    $event->cancel();
                    return;
                }

                switch (true) {
                    case $item instanceof Nemo:
                        if (CooldownManager::hasCooldown($item)) {
                            $damager->sendTip($damager->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => CooldownManager::getCooldown($item) - time()])->__toString());
                            $event->cancel();
                        } elseif (CooldownManager::getCooldownConfig($item->getId())) {
                            $event->getEntity()->knockBack($vector->getX(), $vector->getZ(), Core::getInstance()->getConfig()->getNested("items.nemo.horizontal", 2), Core::getInstance()->getConfig()->getNested("items.nemo.vertical", 0.50));
                            $item = CooldownManager::setCooldown($item, null);
                            $damager->getInventory()->setItemInHand($item);
                        }
                        break;
                    case $item instanceof Spell:
                        if (CooldownManager::hasCooldown($item)) {
                            $damager->sendTip($damager->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => CooldownManager::getCooldown($item) - time()])->__toString());
                            $event->cancel();
                        } elseif (CooldownManager::getCooldownConfig($item->getId())) {
                            $item = CooldownManager::setCooldown($item, null);
                            $damager->getInventory()->setItemInHand($item);
                        }

                        if ($item->getName() == SpellUtils::SPELL_LIGHTNING_NAME) {
                        } elseif ($item->getName() == SpellUtils::SPELL_REPULSION_NAME) {
                            $target = $event->getEntity();
                            $target->knockBack($vector->getX(), $vector->getZ(), Core::getInstance()->getConfig()->getNested("items.spell.repulsion.horizontal", 1.5), Core::getInstance()->getConfig()->getNested("items.spell.repulsion.vertical", 0.50));
                        } elseif ($item->getName() == SpellUtils::SPELL_ATTRACTION_NAME) {
                            $target = $event->getEntity();
                            $target->knockBack($vector->getX(), $vector->getZ(), Core::getInstance()->getConfig()->getNested("items.spell.attraction.horizontal", -1.5), Core::getInstance()->getConfig()->getNested("items.spell.attraction.vertical", -0.50));
                        } elseif ($item->getName() == SpellUtils::SPELL_TELEPORT_NAME) {
                            $target = $event->getEntity();
                            $damager->teleport($target->getPosition());
                        } elseif ($item->getName() == SpellUtils::SPELL_BLINDNESS_NAME) {
                            $target = $event->getEntity();
                            if ($target instanceof LegacyPlayer) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 100, 3));
                            }
                        }
                    default:
                        break;
                }
            }
            $event->setAttackCooldown(Core::getInstance()->getConfig()->getNested("knockback.attack_cooldown", 10));
        }
    }
}