<?php

namespace Legacy\ThePit\Commands;

use pocketmine\command\CommandSender;

class WelcomeCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            //TODO: Faire la commande
        }
    }
}
