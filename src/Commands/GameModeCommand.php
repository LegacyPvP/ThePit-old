<?php

namespace Legacy\ThePit\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

final class GameModeCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof Player){
                if(isset($args[0])){
                    try {
                        $target = $sender;
                        another:
                        if(isset($args[1]) and $sender instanceof Player){
                            $target = Server::getInstance()->getPlayerByPrefix($args[1]);
                            if(!$target){
                                throw new CommandException("messages.commands.target-not-player");
                            }
                        }
                        else throw new CommandException("messages.commands.sender-not-player");
                        $target->setGamemode(match ($args[0]){
                            "0", "survival" => GameMode::SURVIVAL(),
                            "1", "creative" => GameMode::CREATIVE(),
                            "2", "adventure" => GameMode::ADVENTURE(),
                            "3", "spectator" => GameMode::SPECTATOR(),
                            default => throw new CommandException("messages.commands.gamemode.invalid-gamemode", ["{gamemode}" => $args[0]])
                        });
                    }
                    catch (CommandException $e){
                        $this->getSenderLanguage($sender)->getMessage($e->getMessage())->send($sender);
                    }
                }
                else {
                    $sender->sendMessage($this->getUsage());
                }
            }
            else {
                goto another;
            }
        }
    }
}