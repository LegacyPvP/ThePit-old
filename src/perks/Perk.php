<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\player\LegacyPlayer;

abstract class Perk
{
    public function __construct(private string $name, private string $description, private int $price)
    {
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function getDescription(): string
    {
        return $this->description;
    }

    final public function getPrice(): string
    {
        return $this->price;
    }

    public function canStart(LegacyPlayer $player): bool
    {
        return true;
    }

    abstract public function onEvent(): string;
}