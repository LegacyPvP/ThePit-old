<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

final class MessageCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if(isset($args[0])){
                $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                if($target instanceof LegacyPlayer){
                    var_dump($target->getPlayerProperties()->getNestedProperties("settings.blocked_players"));
                    if($target->isOnline() and $target->getPlayerProperties()->getNestedProperties("settings.allow_private_messages") == true){
                        if(substr($sender->getName(), $target->getPlayerProperties()->getNestedProperties("settings.blocked_players"))){
                            $this->getSenderLanguage($sender)->getMessage("messages.commands.message.sender-blocked", [
                                "{target}" => $target->getName()
                            ], ServerUtils::PREFIX_3)->send($sender);
                        }else{
                            if(isset($args[1])){
                                $message = $args[1];
                                $packet = new PlaySoundPacket();
                                $packet->soundName = "random.orb";
                                $packet->x = $target->getLocation()->getX();
                                $packet->y = $target->getLocation()->getY();
                                $packet->z = $target->getLocation()->getZ();
                                $packet->pitch = $target->getLocation()->getPitch();
                                $packet->volume = 1;
                                $target->getNetworkSession()->sendDataPacket($packet);
                                $target->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.message.target-message", [
                                    "{player}" => $sender->getName(),
                                    "{message}" => $message
                                ], ServerUtils::PREFIX_3));
                                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.message.sender-message", [
                                    "{player}" => $target->getName(),
                                    "{message}" => $message
                                ], ServerUtils::PREFIX_3));
                            }else{
                                $sender->sendMessage($this->getUsage());
                            }
                        }
                    }else{
                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.message.sender-blocked", [
                            "{player}" => $target->getName()
                        ], ServerUtils::PREFIX_3));
                    }
                }else{
                    $this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-found", [], ServerUtils::PREFIX_2)->send($sender);
                }
            }else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}
