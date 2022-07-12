<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;

class PingCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if(isset($args[0])){
                $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                if($target instanceof LegacyPlayer){
                    $target_ping = $target->getNetworkSession()->getPing();
                    if ($target_ping < 100) {
                        $target_ping = "§a{$target_ping}ms";
                    }
                    if ($target_ping >= 100 and $target_ping < 300) {
                        $target_ping = "§6{$target_ping}ms";
                    }
                    if ($target_ping >= 300) {
                        $target_ping = "§c{$target_ping}ms";
                    }

                    $sender->getLanguage()->getMessage("messages.commands.ping.target-success")->send($sender, [
                        "{target}" => $target->getName(),
                        "{ping}" => $target_ping
                    ]);
                }else{
                    $sender->getLanguage()->getMessage("messages.commands.ping.target-invalid")->send($sender);
                }

            }else{
                $sender_ping = $sender->getNetworkSession()->getPing();
                if ($sender_ping < 100) {
                    $sender_ping = "§a{$sender_ping}ms";
                }
                if ($sender_ping >= 100 and $sender_ping < 300) {
                    $sender_ping = "§6{$sender_ping}ms";
                }
                if ($sender_ping >= 300) {
                    $sender_ping = "§c{$sender_ping}ms";
                }

                $sender->getLanguage()->getMessage("messages.commands.ping.sender-success")->send($sender, [
                    "{ping}" => $sender_ping
                ]);
            }
        }
    }
}