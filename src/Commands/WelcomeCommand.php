<?php

namespace Legacy\ThePit\commands;

use pocketmine\command\CommandSender;

final class WelcomeCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            //TODO: Faire la commande
        }
    }
}
