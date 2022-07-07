<?php

namespace Legacy\ThePit\Objects;

final class ScoreBoard
{
    public function __construct(private string $name, private array $lines, private int $priority)
    {
    }

}