<?php

namespace Legacy\ThePit\Objects;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\Particle;
use pocketmine\world\sound\XpCollectSound;

final class SnowballProjectile extends \pocketmine\entity\projectile\Snowball
{
    protected ?Particle $projectile = null;
    /** @var Player[]|null */
    private ?array $players;

    /**
     * @param Player[]|null $players
     */
    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null, ?array $players = null){
        parent::__construct($location, $shootingEntity, $nbt);
        $this->players = $players;
        if($this->players !== null){
            foreach($this->players as $p){
                $this->spawnTo($p);
            }
        }else{
            $this->spawnToAll();
        }
        if($shootingEntity !== null){
            $this->setMotion($shootingEntity->getDirectionVector()->multiply(2.5));
        }
        if($shootingEntity instanceof Player){
            $this->projectile = new AngryVillagerParticle();
        }
    }

    public function entityBaseTick(int $tickDiff = 1) : bool{
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if($owner === null || !$owner->isAlive() || $owner->isClosed() || $owner->getWorld() !== $this->getWorld() || $this->ticksLived > 60){
            $this->close();
        }
        if($this->projectile !== null && !$this->isFlaggedForDespawn() && $this->isAlive()){
            $this->getWorld()->addParticle($this->lastLocation->subtractVector($this->lastMotion), $this->projectile, $this->players);
        }
        return $hasUpdate;
    }

    public function canCollideWith(Entity $entity) : bool{
        return parent::canCollideWith($entity);
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
        parent::onHitEntity($entityHit, $hitResult);
        if(($owner = $this->getOwningEntity()) !== null && $owner->getId() !== $entityHit->getId() && $owner instanceof Player && !$entityHit->isSilent()){
            $owner->broadcastSound(new XpCollectSound(), [$owner]);
        }
    }
}