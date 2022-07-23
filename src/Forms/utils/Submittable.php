<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);


namespace Legacy\ThePit\Forms\utils;


use pocketmine\player\Player;

trait Submittable
{
    use SubmitListener;

    public function notifySubmit(Player $player): void
    {
        $this->executeSubmitListener($player);
        $this->onSubmit($player);
    }

    protected function onSubmit(Player $player): void
    {
    }

}