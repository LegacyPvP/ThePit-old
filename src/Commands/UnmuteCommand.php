<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Managers\MuteManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
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
                        MuteManager::removeMute($target);
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