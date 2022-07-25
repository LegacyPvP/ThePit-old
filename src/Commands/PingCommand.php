<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\exceptions\LanguageException;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class PingCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                $target = isset($args[0]) ? $sender->getServer()->getPlayerByPrefix($args[0]) : $sender;
                if (!$target instanceof Player) throw new LanguageException("messages.commands.target-not-found", [], ServerUtils::PREFIX_2);
                $target_ping = $target->getNetworkSession()->getPing();
                match ($target->getName()) {
                    $sender->getName() => throw new LanguageException("messages.commands.ping.sender-success", [
                        "{ping}" => match (true) {
                            $target_ping < 100 => "§a$target_ping",
                            $target_ping < 300 => "§e$target_ping",
                            default => "§c$target_ping",
                        }
                    ], ServerUtils::PREFIX_3),
                    default => throw new LanguageException("messages.commands.ping.target-success", [
                        "{target}" => $target->getName(),
                        "{ping}" => match (true) {
                            $target_ping < 100 => "§a$target_ping",
                            $target_ping < 300 => "§e$target_ping",
                            default => "§c$target_ping",
                        }
                    ], ServerUtils::PREFIX_3)
                };
            } catch (LanguageException $exception) {
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }
        }
    }
}