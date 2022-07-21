<?php

namespace Legacy\ThePit\Test;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\BowShootSound;

final class Bow extends \pocketmine\item\Bow
{
    public function onReleaseUsing(Player $player) : ItemUseResult{
        /*$arrow = VanillaItems::ARROW();
        $inventory = match(true){
            $player->getOffHandInventory()->contains($arrow) => $player->getOffHandInventory(),
            $player->getInventory()->contains($arrow) => $player->getInventory(),
            default => null
        };

        if($player->hasFiniteResources() && $inventory === null){
            return ItemUseResult::FAIL();
        }

        $location = $player->getLocation();

        $diff = $player->getItemUseDuration();
        $p = $diff / 20;
        $baseForce = min((($p ** 2) + $p * 2) / 3, 1);

        $entity = new Arrow(Location::fromObject(
            $player->getEyePos(),
            $player->getWorld(),
            ($location->yaw > 180 ? 360 : 0) - $location->yaw,
            -$location->pitch
        ), $player, $baseForce >= 1);
        $entity->setMotion($player->getDirectionVector());

        $infinity = $this->hasEnchantment(VanillaEnchantments::INFINITY());
        if($infinity){
            $entity->setPickupMode(Arrow::PICKUP_CREATIVE);
        }
        if(($punchLevel = $this->getEnchantmentLevel(VanillaEnchantments::PUNCH())) > 0){
            $entity->setPunchKnockback($punchLevel);
        }
        if(($powerLevel = $this->getEnchantmentLevel(VanillaEnchantments::POWER())) > 0){
            $entity->setBaseDamage($entity->getBaseDamage() + (($powerLevel + 1) / 2));
        }
        if($this->hasEnchantment(VanillaEnchantments::FLAME())){
            $entity->setOnFire(intdiv($entity->getFireTicks(), 20) + 100);
        }
        $ev = new EntityShootBowEvent($player, $this, $entity, $baseForce * 3);

        if($baseForce < 0.1 || $diff < 5 || $player->isSpectator()){
            $ev->cancel();
        }

        $ev->call();

        $entity = $ev->getProjectile(); //This might have been changed by plugins

        if($ev->isCancelled()){
            $entity->flagForDespawn();
            return ItemUseResult::FAIL();
        }

        $entity->setMotion($entity->getMotion()->multiply($ev->getForce()));

        if($entity instanceof Projectile){
            $projectileEv = new ProjectileLaunchEvent($entity);
            $projectileEv->call();
            if($projectileEv->isCancelled()){
                $ev->getProjectile()->flagForDespawn();
                return ItemUseResult::FAIL();
            }

            $ev->getProjectile()->spawnToAll();
            $location->getWorld()->addSound($location, new BowShootSound());
        }else{
            $entity->spawnToAll();
        }

        if($player->hasFiniteResources()){
            if(!$infinity){ //TODO: tipped arrows are still consumed when Infinity is applied
                $inventory?->removeItem($arrow);
            }
            $this->applyDamage(1);
        }

        return ItemUseResult::SUCCESS();*/
    }
}