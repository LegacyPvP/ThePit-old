<?php

namespace Legacy\ThePit\Managers;

interface IManager
{
    public function load(): void;

    public function init(): void;

    public function getAll(): array;

    public function get(string $name): ?object;

}