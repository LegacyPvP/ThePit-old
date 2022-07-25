<?php

namespace Legacy\ThePit\managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\objects\Prestige;
use Legacy\ThePit\utils\PrestigesUtils;

final class PrestigesManager extends Managers
{
    /**
     * @var Prestige[]
     */
    private array $levels = [];

    /**
     * @return Prestige[]
     */
    #[Pure] public function getAll(): array
    {
        return [
            new Prestige(PrestigesUtils::PRESTIGE_LEVELS_REACH_1, PrestigesUtils::PRESTIGE_LEVEL_1, PrestigesUtils::PRESTIGE_1),
        ];
    }

    public function init(): void
    {
        foreach ($this->getAll() as $level) {
            $this->levels[$level->getName()] = $level;
            Core::getInstance()->getLogger()->notice("[PRESTIGES] Loaded");
        }
    }

    public function get(string $name): ?Prestige
    {
        return $this->levels[$name] ?? reset($this->levels) ?: null;
    }
}
