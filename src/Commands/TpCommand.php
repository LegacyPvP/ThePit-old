<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Exceptions\LanguageException;
use pocketmine\command\CommandSender;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;

final class TpCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof Player){
                try {
                    if(isset($args[0]) and !isset($args[1])){
                        $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                        if($target instanceof Player){
                            $sender->teleport($target->getPosition());
                            throw new LanguageException("messages.commands.tp.success", ["{player}" => $target->getName()]);
                        }
                        else if($target instanceof OfflinePlayer and Server::getInstance()->getOfflinePlayer($target->getName())) throw new LanguageException("messages.commands.target-not-found", ["{player}" => $target->getName()]);
                        else throw new LanguageException("messages.commands.target-not-player", ["{player}" => $args[0]]);
                    }
                    else if(isset($args[0], $args[1])){
                        $player1 = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                        $player2 = Server::getInstance()->getPlayerByPrefix($args[1]) ?? Server::getInstance()->getOfflinePlayer($args[1]);
                        if($player1 instanceof Player and $player2 instanceof Player){
                            $player1->teleport($player2->getPosition());
                            throw new LanguageException("messages.commands.tp.success", ["{player1}" => $player1->getName(), "{player2}" => $player2->getName()]);
                        }
                        else if($player1 instanceof Player and !$player2 instanceof Player) throw new LanguageException("messages.commands.target-not-found", ["{player}" => $player1->getName()]);
                        else if(!$player1 instanceof Player and $player2 instanceof Player) throw new LanguageException("messages.commands.target-not-found", ["{player}" => $player2->getName()]);
                        else if(!Server::getInstance()->getOfflinePlayerData($player1->getName())) throw new LanguageException("messages.commands.target-not-player", ["{player}" => $player1->getName()]);
                        else throw new LanguageException("messages.commands.target-not-player", ["{player}" => $player2->getName()]);
                    }
                    else {
                        throw new LanguageException($this->getUsage(), [], false);
                    }
                }
                catch (LanguageException $exception){
                    $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
                }
            }
            else {
                $this->getSenderLanguage($sender)->getMessage("messages.commands.sender-not-player")->send($sender);
            }
        }
    }
}