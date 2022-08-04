<?php

namespace Legacy\ThePit\providers;

use Legacy\ThePit\perks\Perk;
use Legacy\ThePit\player\LegacyPlayer;

final class PerksProvider
{
    /**
     * @var Perk[]
     */
    private array $perks = [];

    public function __construct(private LegacyPlayer $player)
    {
    }

    public function getAll(): array
    {
        return $this->perks;
    }

    public function add(Perk $perk): void
    {
        $this->perks[$perk->getName()] = $perk;
    }

    public function remove(Perk $perk): void
    {
        unset($this->perks[$perk->getName()]);
    }

    public function onEvent(string $class)
    {
        foreach ($this->getAll() as $perk) {
            if ($perk->onEvent() === $class and $perk->canStart($this->player)) {
                $perk->start($this->player);
            }
        }
    }
}