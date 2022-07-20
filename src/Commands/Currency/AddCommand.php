<?php

namespace Legacy\ThePit\Commands\Currency;

use Legacy\ThePit\Commands\Commands;
use Legacy\ThePit\Currencies\Currency;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

class AddCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof LegacyPlayer){
                if(isset($args[0]) and isset($args[1]) and isset($args[2])){
                    $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                    $amount = $args[1];
                    $currency = $args[2];
                    if($target instanceof LegacyPlayer){
                        if($target->isOnline()){
                            if(is_numeric($amount)) {
                                switch($currency){
                                    case "stars":
                                    case "étoiles":
                                    case "etoiles":
                                    case "star":
                                    case "étoile":
                                    case "etoile":
                                        $target->addStars($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.add.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        var_dump($target->getStars());
                                        break;
                                    case "gold":
                                    case "golds":
                                    case "or":
                                        $target->addGold($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.add.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        break;
                                    case "vote":
                                    case "votecoins":
                                        $target->addVoteCoins($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.add.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        break;
                                    case "crédits":
                                    case "crédit":
                                    case "credits":
                                    case "credit":
                                        $target->addCredits($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.add.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        break;
                                }
                            }else{
                                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.add.invalid-amount", [], ServerUtils::PREFIX_2));
                            }
                        }else{
                            $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-found", [], ServerUtils::PREFIX_2));
                        }
                    }else{
                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-found", [], ServerUtils::PREFIX_2));
                    }
                }else{
                    $sender->sendMessage($this->getUsage());
                }
            }else{
                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.sender-not-player", [], ServerUtils::PREFIX_2));
            }
        }
    }
}
