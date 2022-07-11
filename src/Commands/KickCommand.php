<?php

namespace Legacy\ThePit\Commands;

use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

final class KickCommand extends Commands
{
    /**
     * @throws Exception
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0], $args[1])){
                $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                if($target instanceof Player){
                    $reason = implode(" ", array_slice($args, 1));
                    $target->kick(
                        $target->getLanguage()->getMessage("messages.commands.kick.kicked",
                            [
                                "{player}" => $sender->getName(),
                                "{reason}" => $reason
                            ]
                        )
                    );
                }
                else {
                    $sender_language->getMessage("messages.commands.target-not-player")->send($sender);
                }
            }
            else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }

}