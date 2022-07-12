<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

final class GlobalMuteCommand extends Commands {

    /**
     * @throws \JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0])){
                $mode = (bool)$args[0];
                if(is_bool($mode)){
                    ServerUtils::setGlobalMute($mode);
                    if(!ServerUtils::$global_mute){
                        $sender_language->getMessage("messages.commands.globalmute.unmuted")->send($sender);
                    } else {
                        $sender_language->getMessage("messages.commands.globalmute.muted")->send($sender);
                    }
                } else {
                    $sender->sendMessage($this->getUsage());
                }
            }else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}
