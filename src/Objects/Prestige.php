<?php

namespace Legacy\ThePit\Objects;

final class Prestige {

    private array $levels;
    private string $name;
    private string $nametag;

    public function __construct(array $levels, string $name, string $nametag)
    {
        $this->levels = $levels;
        $this->name = $name;
        $this->nametag = $nametag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    /**
     * @return string
     */
    public function getNametag(): string
    {
        return $this->nametag;
    }
}
