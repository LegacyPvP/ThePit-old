<?php

namespace Legacy\ThePit\Listeners;

use Legacy\ThePit\Core;
use Legacy\ThePit\Items\List\Nemo;
use Legacy\ThePit\Items\List\Spell;
use Legacy\ThePit\Managers\Managers;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Tasks\CombatTask;
use Legacy\ThePit\Utils\SpellUtils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent as ClassEvent;
use pocketmine\event\Listener;

final class EntityDamageByEntityEvent implements Listener
{
    /** @var array<string, array<string, bool|int>> $cachedData */
    public static array $cachedData = [];

    public function onEvent(ClassEvent $event): void
    {
        $event->setKnockBack(0);
        $damager = $event->getDamager();
        $target = $event->getEntity();
        if ($target->getId() !== $event->getDamager()?->getId() &&
            $target instanceof LegacyPlayer &&
            $event->getModifier(\pocketmine\event\entity\EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN) >= 0.0 &&
            $damager instanceof LegacyPlayer) {

            $vector = $damager->getDirectionVector();
            $item = $event->getDamager()->getInventory()->getItemInHand();

            if ($damager->isImmobile()) {
                $event->cancel();
                return;
            }

            if ($damager->isInCombat() and $target->isInCombat()) {
                if (!str_contains($target->getName(), $damager->targetName) and !str_contains($damager->getName(), $target->targetName)) {
                    $damager->getLanguage()->getMessage("messages.combat.already_in_combat")->send($damager);
                    $event->cancel();
                } else {
                    $damager->setInCombat(true, $target);
                    $target->setInCombat(true, $damager);
                    Core::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTask($target), 20);
                    Core::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTask($damager), 20);
                }
            }

            switch (true) {
                case $item instanceof Nemo:
                    if (Managers::COOLDOWNS()->hasCooldown($item)) {
                        $damager->sendTip($damager->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => Managers::COOLDOWNS()->getCooldown($item) - time()])->__toString());
                        $event->cancel();
                    } elseif (Managers::COOLDOWNS()->getCooldownConfig($item->getId())) {
                        $event->getEntity()->knockBack($vector->getX(), $vector->getZ(), Managers::DATA()->get("config")->getNested("items.nemo.horizontal", 2), Managers::DATA()->get("config")->getNested("items.nemo.vertical", 0.50));
                        $item = Managers::COOLDOWNS()->setCooldown($item, null);
                        $damager->getInventory()->setItemInHand($item);
                    }
                    break;
                case $item instanceof Spell:
                    if (Managers::COOLDOWNS()->hasCooldown($item)) {
                        $damager->sendTip($damager->getLanguage()->getMessage("messages.interactions.cooldown", ["{timeleft}" => Managers::COOLDOWNS()->getCooldown($item) - time()])->__toString());
                        $event->cancel();
                    } elseif (Managers::COOLDOWNS()->getCooldownConfig($item->getId())) {
                        $item = Managers::COOLDOWNS()->setCooldown($item, null);
                        $damager->getInventory()->setItemInHand($item);
                    }

                    if ($item->getName() == SpellUtils::SPELL_LIGHTNING_NAME) {
                    } elseif ($item->getName() == SpellUtils::SPELL_REPULSION_NAME) {
                        $target = $event->getEntity();
                        $target->knockBack($vector->getX(), $vector->getZ(), Managers::DATA()->get("config")->getNested("items.spell.repulsion.horizontal", 1.5), Managers::DATA()->get("config")->getNested("items.spell.repulsion.vertical", 0.50));
                    } elseif ($item->getName() == SpellUtils::SPELL_ATTRACTION_NAME) {
                        $target = $event->getEntity();
                        $target->knockBack($vector->getX(), $vector->getZ(), Managers::DATA()->get("config")->getNested("items.spell.attraction.horizontal", -1.5), Managers::DATA()->get("config")->getNested("items.spell.attraction.vertical", -0.50));
                    } elseif ($item->getName() == SpellUtils::SPELL_TELEPORT_NAME) {
                        $target = $event->getEntity();
                        $damager->teleport($target->getPosition());
                    } elseif ($item->getName() == SpellUtils::SPELL_BLINDNESS_NAME) {
                        $target = $event->getEntity();
                        if ($target instanceof LegacyPlayer) {
                            $target->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 100, 3));
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        $event->setAttackCooldown(Managers::DATA()->get("config")->getNested("knockback.attack_cooldown", 10));
    }
}