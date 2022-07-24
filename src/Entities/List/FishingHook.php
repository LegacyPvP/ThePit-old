<?php

namespace Legacy\ThePit\Entities\List;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Items\List\FishingRod;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemIds;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\Random;

class FishingHook extends Projectile
{
    protected $gravity = 0.1;

    public static function getNetworkTypeId(): string
    {
        return EntityIds::FISHING_HOOK;
    }

    public function __construct(Location $location, ?Entity $entity)
    {
        parent::__construct($location, $entity, CompoundTag::create());
        if ($entity instanceof Player) {
            $this->setPosition($this->getLocation()->add(0, $entity->getEyeHeight() - 0.1, 0));
            $this->setMotion($entity->getDirectionVector()->multiply(0.4));
            $this->handle($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
        }
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void
    {
        //NOTHING
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if ($owner instanceof Player) {
            if (!$owner->getInventory()->getItemInHand() instanceof FishingRod or !$owner->isAlive() or $owner->isClosed())
                $this->delete();
        } else $this->delete();

        return $hasUpdate;
    }

    public function attack(EntityDamageEvent $source): void
    {
        parent::attack($source);
        $player = $source->getEntity();
        if (!$player instanceof Player || $source->getCause() !== EntityDamageEvent::CAUSE_FALL) return;
        if ($player->getInventory()->getItemInHand()->getId() === ItemIds::FISHING_ROD) {
            $source->cancel();
        }
    }

    public function delete(): void
    {
        $this->flagForDespawn();
        $owner = $this->getOwningEntity();
        if ($owner instanceof LegacyPlayer) {
            $owner->setFishing(null);
        }
    }
    public function handle(float $x, float $y, float $z, float $f1, float $f2): void
    {
        $rand = new Random();
        $f = sqrt($x * $x + $y * $y + $z * $z);
        $x = $x / $f;
        $y = $y / $f;
        $z = $z / $f;
        $x = $x + $rand->nextSignedFloat() * 0.0074 * $f2;
        $y = $y + $rand->nextSignedFloat() * 0.0074 * $f2;
        $z = $z + $rand->nextSignedFloat() * 0.0074 * $f2;
        $x = $x * $f1;
        $y = $y * $f1;
        $z = $z * $f1;
        $this->motion->x += $x;
        $this->motion->y += $y;
        $this->motion->z += $z;
    }

    #[Pure] public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.25, 0.25);
    }
}