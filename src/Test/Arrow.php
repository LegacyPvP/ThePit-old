<?php

namespace Legacy\ThePit\Test;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\LavaDripParticle;
use pocketmine\world\particle\Particle;
use pocketmine\world\sound\XpCollectSound;

final class Arrow extends \pocketmine\entity\projectile\Arrow
{
    protected $damage = 1.0;
    protected ?Particle $projectile = null;
    /** @var Player[]|null */
    private ?array $players;

    /**
     * @param Player[]|null $players
     */
    public function __construct(Location $location, ?Entity $shootingEntity, bool $critical, ?CompoundTag $nbt = null, ?array $players = null)
    {
        parent::__construct($location, $shootingEntity, $critical, $nbt);
        $this->players = $players;
        if ($this->players !== null) {
            foreach ($this->players as $p) {
                $this->spawnTo($p);
            }
        } else {
            $this->spawnToAll();
        }
        if ($shootingEntity !== null) {
            $this->setMotion($shootingEntity->getDirectionVector());
        }
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if ($owner === null || !$owner->isAlive() || $owner->isClosed() || $owner->getWorld() !== $this->getWorld() || $this->ticksLived > 60) {
            $this->close();
        }
        if ($this->projectile !== null && !$this->isFlaggedForDespawn() && $this->isAlive()) {
            $this->getWorld()->addParticle($this->lastLocation->subtractVector($this->lastMotion), $this->projectile, $this->players);
        }
        $this->projectile = new LavaDripParticle();
        return $hasUpdate;
    }

    public function canCollideWith(Entity $entity): bool
    {
        $player = $this->getOwningEntity();
        return parent::canCollideWith($entity);
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void
    {
        parent::onHitBlock($blockHit, $hitResult);
        $this->flagForDespawn();
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void
    {
        parent::onHitEntity($entityHit, $hitResult);
        if (($owner = $this->getOwningEntity()) !== null && $owner instanceof Player && !$entityHit->isSilent()) {
            if ($entityHit instanceof Player) {
                if ($owner->getId() !== $entityHit->getId()) {
                    $owner->broadcastSound(new XpCollectSound(), [$owner]);
                    $owner->sendMessage(TextFormat::RED . $entityHit->getDisplayName() . TextFormat::YELLOW . " is now on " . TextFormat::RED . round($entityHit->getHealth() / 2, 1) . TextFormat::YELLOW . " HP!");
                }
            }
        }
    }
}