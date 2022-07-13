<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Managers\MuteManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;
use pocketmine\Server;

final class UnmuteCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0])){
                $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                if($target instanceof LegacyPlayer) {
                    MuteManager::removeMute($target);
                    $sender_language->getMessage("messages.commands.unmute.success", ["{player}" => $target->getName()])->send($sender);
                }
                else {
                    $this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-player")->send($sender);
                }
            }
            else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }

}