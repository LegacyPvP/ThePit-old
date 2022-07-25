<?php

declare(strict_types=1);

namespace Legacy\ThePit\traits;

trait CustomItemTrait
{
    public function getRuntimeId(int $id): int
    {
        return $id + ($id > 0 ? 5000 : -5000);
    }

    public function checkName(string $name): string
    {
        $name = strtolower($name);
        $str = preg_replace('/\s+/', '-', $name);
        return "item." . $str . ".name";
    }
}