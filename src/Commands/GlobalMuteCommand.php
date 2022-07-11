<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use pocketmine\command\CommandSender;

class GlobalMuteCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0])){
                $mode = $args[0];
                if(is_bool($mode)){
                    $global_mute = Core::getInstance()->getConfig()->getNested("global-mute");
                    if($global_mute === false) {
                        Core::getInstance()->getConfig()->setNested("global-mute", true);
                        Core::getInstance()->getConfig()->save();
                        $sender->sendMessage($sender_language->getMessage("messages.commands.global-mute.muted"));
                    }elseif($global_mute === true){
                        Core::getInstance()->getConfig()->setNested("global-mute", false);
                        Core::getInstance()->getConfig()->save();
                        $sender->sendMessage($sender_language->getMessage("messages.commands.global-mute.unmuted"));
                    }else{
                        $sender->sendMessage($sender_language->getMessage("messages.commands.global-mute.error"));
                    }
                }else {
                    $sender->sendMessage($this->getUsage());
                }
            }else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}
