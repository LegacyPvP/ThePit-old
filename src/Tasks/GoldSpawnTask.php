<?php

namespace Legacy\ThePit\Tasks;

use Legacy\ThePit\Core;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\World;

final class GoldSpawnTask extends Task
{
    private array $x;
    private array $y;
    private array $z;
    private ?World $world;

    public function __construct()
    {
        $this->x = Core::getInstance()->getConfig()->getNested("goldspawn.x", [0, 0]);
        $this->y = Core::getInstance()->getConfig()->getNested("goldspawn.y", [0, 0]);
        $this->z = Core::getInstance()->getConfig()->getNested("goldspawn.z", [0, 0]);
        $this->world = Core::getInstance()->getServer()->getWorldManager()->getWorldByName(
            Core::getInstance()->getConfig()->getNested("goldspawn.world", "world")
        );
    }

    public function onRun(): void
    {
        $block = $this->findBlock();
        $item = VanillaItems::GOLD_INGOT();
        $this->getWorld()->dropItem($block->getPosition()->ceil(), $item);
    }

    public function findBlock(): Block {
        $x = $this->getX();
        $z = $this->getZ();
        $x = $x[array_rand($x)];
        $z = $z[array_rand($z)];
        $block = $this->getWorld()->getBlock($this->getWorld()->getSafeSpawn(new Vector3($x, max($this->getY()), $z)));
        if(!$block instanceof Air){
            return $block;
        }
        return $this->findBlock();
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
}