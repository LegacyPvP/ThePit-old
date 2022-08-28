<?php

namespace Legacy\ThePit\tiles\list;

use Legacy\ThePit\Core;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\RewardUtils;
use pocketmine\block\tile\Chest;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

class CrateTile extends Chest
{
    private string $crateName;
    private int $time = 5;
    public bool $inUse;

    public function __construct(World $world, Vector3 $pos, protected RewardUtils $reward)
    {
        parent::__construct($world, $pos);
    }

    public function setRealName(string $name)
    {
        $this->crateName = $name;
    }

    public function getName(): string
    {
        return $this->crateName;
    }


    public function getContent(): array
    {
        return $this->getInventory()->getContents();
    }

    public function giveReward(LegacyPlayer $player): void
    {
        if (!$this->inUse) {
            if (in_array($player->getInventory()->getItemInHand()->getId() . ":" . $player->getInventory()->getItemInHand()->getMeta(), Main::getInstance()->getCfg()["crates"][$this->getRealName()]["keys"])) {
                $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
                $this->time = 5;
                $this->inUse = true;
                Core::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($player): void {
                    if (!$player->isConnected()) {
                        $this->time = 5;
                        $this->inUse = false;
                        throw new CancelTaskException();
                    }
                    if ($this->time == 0) {
                        $this->time = 5;
                        $this->reward->request($player);
                        throw new CancelTaskException();
                    }
                    $player?->sendMessage("§aYou will receive a reward in §e" . $this->time . "§a seconds!");
                    $this->time--;
                }), 20);
            } else {
                $player?->sendMessage("§cYou don't have the needed key to open this crate!");
            }
            return;
        }
        $player->sendMessage("§cCrate is in use!");
    }

    protected function writeSaveData(CompoundTag $nbt): void
    {
        parent::writeSaveData($nbt);
        $nbt->setString("CrateName", $this->crateName);
    }

    public function readSaveData(CompoundTag $nbt): void
    {
        parent::readSaveData($nbt);
        $this->crateName = $nbt->getString("CrateName");
    }
}