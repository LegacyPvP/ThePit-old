<?php

namespace Legacy\ThePit\Entities\List;

use Legacy\ThePit\Items\List\FishingRod;
use Legacy\ThePit\Managers\DataManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\Random;

class FishingHook extends Projectile
{
    protected $gravity = 0.1;
    private int $dropsTime;
    private int $lostDropTime;
    private Item $drop;


    public static function getNetworkTypeId(): string
    {
        return EntityIds::FISHING_HOOK;
    }

    public function __construct(Location $location, ?Entity $entity)
    {
        if ($entity instanceof Player) {
            $this->setOwningEntity($entity);
            $this->setPosition($this->getLocation()->add(0, $entity->getEyeHeight() - 0.1, 0));
            $this->setMotion($entity->getDirectionVector()->multiply(0.4));
            $this->handle($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
            $this->dropsTime = mt_rand(min(DataManager::getProvider("config")->getNested("items.fishingRod.drops-interval")[0], DataManager::getProvider("config")->getNested("items.fishingRod.drops-interval")[1]), max(DataManager::getProvider("config")->getNested("items.fishingRod.drops-interval")[0], DataManager::getProvider("config")->getNested("items.fishingRod.drops-interval")[1]));
            $this->lostDropTime = mt_rand(min(DataManager::getProvider("config")->getNested("items.fishingRod.lost-drop-interval")[0], DataManager::getProvider("config")->getNested("items.fishingRod.lost-drop-interval")[1]), max(DataManager::getProvider("config")->getNested("items.fishingRod.lost-drop-interval")[0], DataManager::getProvider("config")->getNested("items.fishingRod.lost-drop-interval")[1]));
            while(true)
            {
                $drop = $this->getRandomDrop();
                if($drop != null)
                {
                    $this->drop = $drop;
                    break;
                }
            }
        }
        parent::__construct($location, $entity, CompoundTag::create());
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void
    {
        //-1 durability ?
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isFlaggedForDespawn()) return false;
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if (!$owner instanceof Player) $this->delete();
        if (!$owner->getInventory()->getItemInHand() instanceof FishingRod or !$owner->isAlive() or $owner->isClosed()) $this->delete();
        $this->dropsTime -= $tickDiff;
        if ($this->dropsTime <= 0) {
            $this->lostDropTime -= $tickDiff;
            if($this->lostDropTime <= 0)
            {
                
            }
        }
        return $hasUpdate;
    }


    public function attack(EntityDamageEvent $source): void
    {
        parent::attack($source);
        $player = $source->getEntity();
        if (!$player instanceof Player || $source->getCause() !== EntityDamageEvent::CAUSE_FALL) return;
        if ($player->getInventory()->getItemInHand()->getId() === ItemIds::FISHING_ROD) {
            $this->delete();
            $source->cancel();
        }
    }

    public function delete(): void
    {
        if (!$this->isFlaggedForDespawn()) $this->flagForDespawn();
        $owner = $this->getOwningEntity();
        if ($owner instanceof LegacyPlayer) {
            $owner->setFishing(null);
        }
    }

    private function getRandomDrop(): ?Item
    {
        $drops = DataManager::getProvider("config")->getNested("items.fishingRod.drops");
        foreach ($drops as $drop => $data) {
            if (100 <= $data["chance"]) {
                return ItemFactory::getInstance()->get((int)$data["id"], (int)$data["meta"], (int)$data["amount"])
                    ->setCustomName($drop);
            }
        }
        return null;
    }
    public function handle(float $x, float $y, float $z, float $f1, float $f2): void
    {
        $rand = new Random();
        $f = sqrt($x * $x + $y * $y + $z * $z);
        $x = $x / (float)$f;
        $y = $y / (float)$f;
        $z = $z / (float)$f;
        $x = $x + $rand->nextSignedFloat() * 0.0074 * (float)$f2;
        $y = $y + $rand->nextSignedFloat() * 0.0074 * (float)$f2;
        $z = $z + $rand->nextSignedFloat() * 0.0074 * (float)$f2;
        $x = $x * (float)$f1;
        $y = $y * (float)$f1;
        $z = $z * (float)$f1;
        $this->motion->x += $x;
        $this->motion->y += $y;
        $this->motion->z += $z;
    }


    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.25, 0.25);
    }

    public function getDrop(): Item
    {
        return $this->drop;
    }

    public function getDropsTime(): int
    {
        return $this->dropsTime;
    }

    public function getLostDropTime(): int
    {
        return $this->lostDropTime;
    }
}