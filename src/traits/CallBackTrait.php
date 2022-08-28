<?php

namespace Legacy\ThePit\traits;

trait CallBackTrait
{
    /**
     * @var callable[]
     */
    protected array $callBack = [];

    public function executeCallBack(string $name, ...$args): void
    {
        if (isset($this->callBack[$name])) $this->callBack[$name](...$args);
    }


    public function addCallback(string $name, callable $callback): void
    {
        //pas besoin de vérifier s'il existe ? (on écrase le callback ancien ?)
        if (!isset($this->callBack[$name])) $this->callBack[$name] = $callback;
    }

    public function removeCallBack(string $name): void
    {
        if (isset($this->callBack[$name])) unset($this->callBack[$name]);
    }
}