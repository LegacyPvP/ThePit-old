<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\crate\Crate;

final class CrateManager extends Managers
{
    /**
     * @var Crate[]
     */
    private array $crates;

    public function init(): void
    {
        foreach (self::DATA()->get("config")->get("boxs") as $name => $data) {
            $this->crates[$name] = Crate::create($name, $data);
            Core::getInstance()->getLogger()->notice("[CRATES] Crate: $name Loaded");
        }
    }

    public function get(string $name): ?Crate
    {
        return $this->crates[$name] ?? reset($this->crates) ?: null;
    }

    public function getAll(): array
    {
        return $this->crates;
    }
}
