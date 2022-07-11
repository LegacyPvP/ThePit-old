<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Exceptions\LanguageException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

final class TprCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof Player){
                try {
                    random:
                    $target = Server::getInstance()->getOnlinePlayers()[array_rand(Server::getInstance()->getOnlinePlayers())];
                    if($target->getName() === $sender->getName() and count(Server::getInstance()->getOnlinePlayers()) > 1){
                        goto random;
                    }
                    else if($target->getName() === $sender->getName() and count(Server::getInstance()->getOnlinePlayers()) === 1) throw new LanguageException("messages.commands.tpr.failed");
                    $sender->teleport($target->getPosition());
                    throw new LanguageException("messages.commands.tpr.success", ["{player}" => $target->getName()]);
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