<?php

namespace Legacy\ThePit\entities\list;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\items\list\FishingRod;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
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
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use pocketmine\world\particle\BubbleParticle;

final class FishingHook extends Projectile
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
        parent::__construct($location, $entity, CompoundTag::create());
        if ($entity instanceof Player) {
            $this->setPosition($this->getLocation()->add(0, $entity->getEyeHeight() - 0.1, 0));
            $this->setMotion($entity->getDirectionVector()->multiply(0.4));
            $this->handle($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
            $this->loadDrops();
        }
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void
    {
        //NOTHING
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isFlaggedForDespawn()) return false;
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if (!$owner instanceof Player) {
            $this->delete();
            return false;
        }
        if (!$owner?->getInventory()->getItemInHand() instanceof FishingRod or !$owner?->isAlive() or $owner?->isClosed()) {
            $this->delete();
            return false;
        }
        if ($this->isUnderwater()) {
            $this->motion->y += $this->gravity;
            $this->dropsTime -= $tickDiff;
            if ($this->dropsTime <= 0) {
                $this->motion->y -= $this->gravity;
                //TODO: broadcast qu'une seule fois...
                $this->getWorld()->broadcastPacketToViewers($this->getPosition()->asVector3(),
                    PlaySoundPacket::create(
                        "random.splash",
                        $this->getPosition()->x,
                        $this->getPosition()->y,
                        $this->getPosition()->z,
                        1,
                        1)
                );
                $this->getWorld()->addParticle($this->getLocation(), new BubbleParticle());
                $this->lostDropTime -= $tickDiff;
                if ($this->lostDropTime <= 0) {
                    $this->loadDrops();
                }
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
        $owner = $this->getOwningEntity();
        if ($owner instanceof LegacyPlayer) {
            if($this->dropsTime <= 0 && $this->lostDropTime > 0)
            {
                $owner->getWorld()->dropItem($this->getLocation(), $this->drop);
                //approcher l'item vers le joueur
            }
            $owner->setFishing(null);
        }
        if (!$this->isFlaggedForDespawn()) $this->flagForDespawn();
    }
    private function getRandomDrop(): ?Item
    {
        $drops = Managers::DATA()->get("config")->getNested("items.fishingRod.drops", []);
        foreach ($drops as $drop => $data) {
            if (rand(1, 101) <= $data["chance"]) {
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


    private function loadDrops(): void
    {
        $drops_interval = Managers::DATA()->get("config")->getNested("items.fishingRod.drops-interval", [100, 600]);
        $lost_drop_interval = Managers::DATA()->get("config")->getNested("items.fishingRod.lost-drop-interval", [8, 13]);
        $this->dropsTime = mt_rand(min($drops_interval), max($drops_interval));
        $this->lostDropTime = mt_rand(min($lost_drop_interval), max($lost_drop_interval));
        while (true) {
            $drop = $this->getRandomDrop();
            if ($drop !== null) {
                $this->drop = $drop;
                break;
            }
        }
    }

    #[Pure] public function getInitialSizeInfo(): EntitySizeInfo
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