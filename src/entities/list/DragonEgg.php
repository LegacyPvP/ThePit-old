<?php

namespace Legacy\ThePit\entities\list;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class DragonEgg extends \pocketmine\block\DragonEgg
{
    private int $clicked = 0;
    private int $step = 1;
    private int $max;

    public function __construct(BlockIdentifier $idInfo, string $name, BlockBreakInfo $breakInfo)
    {
        parent::__construct($idInfo, $name, $breakInfo);
        $this->max = mt_rand(5, 9);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        $this->clicked++;
        if($this->clicked >= $this->max){
            $this->teleport();
            $this->clicked = 0;
            $this->step++;
            $this->max = mt_rand(5, 9);
        }
        return true;
    }
}