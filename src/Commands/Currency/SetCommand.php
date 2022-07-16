<?php

namespace Legacy\ThePit\Commands\Currency;

use Legacy\ThePit\Commands\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

class SetCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)){
            if($sender instanceof LegacyPlayer){
                if(isset($args[0]) and isset($args[1]) and isset($args[2])){
                    $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                    $amount = $args[1];
                    $currency = $args[2];
                    if($target instanceof LegacyPlayer){
                        if($target->isOnline()){
                            if(is_numeric($amount)) {
                                switch($currency) {
                                    case "stars":
                                    case "étoiles":
                                    case "etoiles":
                                    case "star":
                                    case "étoile":
                                    case "etoile":
                                        $target->setStars($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.set.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        var_dump($target->getStars());
                                        break;
                                    case "gold":
                                    case "golds":
                                    case "or":
                                        $target->setGold($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.set.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        break;
                                    case "vote":
                                    case "votecoins":
                                        $target->setVoteCoins($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.set.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        break;
                                    case "crédits":
                                    case "crédit":
                                    case "credits":
                                    case "credit":
                                        $target->setCredits($amount);
                                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.set.success", [
                                            "{target}" => $target->getName(),
                                            "{amount}" => $amount
                                        ], ServerUtils::PREFIX_3));
                                        break;
                                }
                            }else{
                                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.set.invalid-amount", [], ServerUtils::PREFIX_2));
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
                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.no-player", [], ServerUtils::PREFIX_2));
            }
        }
    }
}
