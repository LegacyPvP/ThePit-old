<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StatisticsCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof LegacyPlayer){
                if(!isset($args[0])){
                    $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.statistics.success", [
                        "{player}" => $sender->getName(),
                        "{stars}" => $sender->getStars(),
                        "{gold}" => $sender->getGold(),
                        "{votecoins}" => $sender->getVoteCoins(),
                        "{credits}" => $sender->getCredits()
                    ], ServerUtils::PREFIX_4));
                }else{
                    $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                    $offline_target = $sender->getServer()->getOfflinePlayer($args[0]);
                    if($target instanceof LegacyPlayer){
                        if($target->isOnline()){
                            $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.statistics.success", [
                                "{player}" => $target->getName(),
                                "{stars}" => $target->getStars(),
                                "{gold}" => $target->getGold(),
                                "{votecoins}" => $target->getVoteCoins(),
                                "{credits}" => $target->getCredits()
                            ], ServerUtils::PREFIX_4));
                        }elseif($offline_target instanceof LegacyPlayer){
                            $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.statistics.success", [
                                "{player}" => $offline_target->getName(),
                                "{stars}" => $offline_target->getStars(),
                                "{gold}" => $offline_target->getGold(),
                                "{votecoins}" => $offline_target->getVoteCoins(),
                                "{credits}" => $offline_target->getCredits()
                            ], ServerUtils::PREFIX_4));
                        }else{
                            $this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-player", [], ServerUtils::PREFIX_2)->send($sender);
                        }
                    }else{
                        $this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-found", [], ServerUtils::PREFIX_2)->send($sender);
                    }
                }
            }else{
                $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.no-player", [], ServerUtils::PREFIX_4));
            }
        }
    }
}