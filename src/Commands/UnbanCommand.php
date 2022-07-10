<?php

namespace Legacy\ThePit\Commands;

use pocketmine\command\CommandSender;
use pocketmine\Server;

final class UnbanCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0])){
                $target = Server::getInstance()->getOfflinePlayer($args[0]) ??
                    (substr_count($args[0], ".") === 4)
                        ? $args[0]
                        : null;
                if($target){
                    Server::getInstance()->getIPBans()->remove($target);
                    Server::getInstance()->getNameBans()->remove($target);
                    $sender_language->getMessage("messages.commands.unban.success")->send($sender);
                }
                else {
                    $sender_language->getMessage("messages.commands.not-player")->send($sender);
                }
            }
            else {
                $sender->sendMessage($this->getUsage());
            }
        }
        return true;
    }

}