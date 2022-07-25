<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\exceptions\LanguageException;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

final class UnmuteCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                if (isset($args[0])) {
                    $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                    if ($target instanceof LegacyPlayer) {
                        Managers::MUTES()->removeMute($target);
                        throw new LanguageException("messages.commands.unmute.success", ["{player}" => $target->getName()]);
                    } else throw new LanguageException("messages.commands.target-not-player", [], ServerUtils::PREFIX_2);
                } else {
                    $sender->sendMessage($this->getUsage());
                }
            } catch (LanguageException $exception) {
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }
        }
    }

}