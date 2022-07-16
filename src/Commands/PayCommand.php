<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class PayCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof LegacyPlayer){
                if(isset($args[0]) and isset($args[1])){
                    $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                    $amount = $args[1];
                    if($target instanceof LegacyPlayer){
                        if(is_int($amount)){
                            if($sender->hasGold($amount)){
                                $packet = new PlaySoundPacket();
                                $packet->soundName = "random.orb";
                                $packet->x = $target->getLocation()->getX();
                                $packet->y = $target->getLocation()->getY();
                                $packet->z = $target->getLocation()->getZ();
                                $packet->pitch = $target->getLocation()->getPitch();
                                $packet->volume = 1;

                                $target->getNetworkSession()->sendDataPacket($packet);
                                $target->addGold($amount);
                                $sender->removeGold($amount);
                                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.pay.success", [
                                    "{target}" => $target->getName(),
                                    "{amount}" => $amount
                                ], ServerUtils::PREFIX_3));
                                $target->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.pay.received", [
                                    "{player}" => $sender->getName(),
                                    "{amount}" => $amount
                                ], ServerUtils::PREFIX_3));
                            }else{
                                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.pay.not-enough-gold", [
                                    "{amount}" => $amount
                                ], ServerUtils::PREFIX_2));
                            }
                        }else{
                            $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.pay.invalid-amount", [], ServerUtils::PREFIX_2));
                        }
                    }else{
                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-found", [], ServerUtils::PREFIX_2));
                    }
                }else{
                    $sender->sendMessage($this->getUsage());
                }
            }else{
                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.no-player", [], ServerUtils::PREFIX_2));
            }
        }
    }
}