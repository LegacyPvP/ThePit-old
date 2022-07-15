<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;

class ListCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            foreach($sender->getServer()->getOnlinePlayers() as $player){
                if($player instanceof LegacyPlayer){
                    //TODO: Finir
                }
            }
        }
    }
}
