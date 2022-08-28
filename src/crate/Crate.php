<?php

namespace Legacy\ThePit\crate;

use Legacy\ThePit\tiles\list\CrateTile;
use Legacy\ThePit\utils\RewardUtils;
use pocketmine\block\Chest;

final class Crate
{
    private RewardUtils $reward;

    public function __construct(protected string $name, protected array $data)
    {
        $this->reward = RewardUtils::create($this->data["rewards"]["xps"], $this->data["rewards"]["items"], $this->data["rewards"]["money"]);
    }

    public static function create(string $name, array $data): self
    {
        return new self($name, $data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getReward(): RewardUtils{
        return $this->reward;
    }

    public function createCrate(Chest $chest): void
    {
        $tile = $chest->getPosition()->getWorld()->getTile($chest->getPosition());
        if ($tile != null) $chest->getPosition()->getWorld()->removeTile($tile);
        $tile = new CrateTile($chest->getPosition()->getWorld(), $chest->getPosition()->asVector3()->floor(), $this->reward);
        $tile->setRealName($this->name);
        $chest->getPosition()->getWorld()->addTile($tile);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}