<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;

final class NightVisionCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if($sender instanceof LegacyPlayer){
                if($sender->getPlayerProperties()->getNestedProperties('status.nightvision')){
                    $sender->getPlayerProperties()->setNestedProperties('status.nightvision', false);
                    $sender_language->getMessage("messages.commands.nightvision.success-off")->send($sender);
                }else{
                    $sender->getPlayerProperties()->setNestedProperties('status.nightvision', true);
                    $sender_language->getMessage("messages.commands.nightvision.success-on")->send($sender);
                }
            }
        }
    }
}
