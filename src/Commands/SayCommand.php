<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Managers\LanguageManager;
use pocketmine\command\CommandSender;

class SayCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0])){
                if(str_word_count($args[0]) >= 1){
                    $message = implode(" ", array_slice($args, 0));
                    $sender->getServer()->broadcastMessage(LanguageManager::getPrefix() . $message);
                }else{
                    $sender_language->getMessage("messages.commands.say.no-message-valid")->send($sender);
                }
            }else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}