<?php

namespace Legacy\ThePit\tasks;

use Exception;
use Legacy\ThePit\Core;
use Legacy\ThePit\managers\Managers;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\world\World;

final class GoldSpawnTask extends Task
{
    private array $x;
    private array $y;
    private array $z;
    private ?World $world;
    private bool $cleared = false;

    public function __construct()
    {
        $this->x = Managers::DATA()->get("config")->getNested("goldspawn.x", [0, 0]);
        $this->y = Managers::DATA()->get("config")->getNested("goldspawn.y", [0, 0]);
        $this->z = Managers::DATA()->get("config")->getNested("goldspawn.z", [0, 0]);
        Core::getInstance()->getServer()->getWorldManager()->loadWorld(
            Managers::DATA()->get("config")->getNested("goldspawn.world", "Sky")
        );
        $this->world = Core::getInstance()->getServer()->getWorldManager()->getWorldByName(
            Managers::DATA()->get("config")->getNested("goldspawn.world", "Sky")
        );
    }

    /**
     * @throws Exception
     */
    public function onRun(): void
    {
        if ($this->getWorld()?->getEntities() == null or empty($this->getWorld()->getEntities())) {
            throw new CancelTaskException();
        }
        if (!$this->cleared) {
            foreach ($this->getWorld()?->getEntities() as $entity) {
                if (!$entity instanceof ItemEntity) continue;
                $entity->flagForDespawn();
            }
            $this->cleared = true;
        }
        $block = $this->findBlock();
        $item = VanillaItems::GOLD_INGOT();
        ($this->getWorld()->dropItem($block->getPosition()->ceil(), $item))->setDespawnDelay(2400);
    }

    /**
     * @throws Exception
     */
    public function findBlock(): Block
    {
        retry:
        $x = rand(min($this->getX()), max($this->getX()) + 1);
        $z = rand(min($this->getZ()), max($this->getZ()) + 1);
        $block = $this->getSafeBlock($x, $this->getY(), $z);
        if (!$block) goto retry;
        return $block;
    }

    /**
     * @return array
     */
    public function getX(): array
    {
        return $this->x;
    }

    /**
     * @return array
     */
    public function getY(): array
    {
        return $this->y;
    }

    /**
     * @return array
     */
    public function getZ(): array
    {
        return $this->z;
    }

    /**
     * @return World|null
     */
    public function getWorld(): ?World
    {
        return $this->world;
    }

    /**
     * @param int $x
     * @param array $y
     * @param int $z
     * @return Block|null
     */
    private function getSafeBlock(int $x, array $y, int $z): ?Block
    {
        $block = null;
        for ($i = max($y); $i >= min($y); $i--) {
            $block = $this->getWorld()?->getBlockAt($x, $i, $z);
            if ($block instanceof Air) {
                continue;
            }
            break;
        }
        return $block instanceof Air ? null : $block;
    }
}