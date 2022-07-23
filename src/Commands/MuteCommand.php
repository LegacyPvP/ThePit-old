<?php

namespace Legacy\ThePit\Commands;

use DateTime;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Managers\MuteManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use Legacy\ThePit\Utils\TimeUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

final class MuteCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            if (isset($args[0], $args[1])) {
                try {
                    $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                    if ($target instanceof LegacyPlayer) {
                        $time = TimeUtils::strToDate($args[1]) ?? new DateTime("now");
                        $reason = isset($args[2]) ? implode(" ", array_slice($args, 2)) : "Aucune raison donnée.";
                        MuteManager::setMuted($target, $time->getTimestamp(), $reason, $sender->getName());
                        $target->getLanguage()->getMessage("messages.commands.mute.muted", [
                            "{player}" => $sender->getName(),
                            "{date}" => $time->format("d/m/Y H:i:s"),
                            "{reason}" => $reason
                        ], ServerUtils::PREFIX_2);
                        throw new LanguageException("messages.commands.mute.success", [
                            "{player}" => $target->getName(),
                            "{date}" => $time->format("d/m/Y H:i:s"),
                            "{reason}" => $reason
                        ], ServerUtils::PREFIX_3);
                    } else throw new LanguageException("messages.commands.target-not-found");
                } catch (LanguageException $exception) {
                    $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
                }
            } else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}