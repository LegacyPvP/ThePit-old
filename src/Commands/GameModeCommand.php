<?php

namespace Legacy\ThePit\Commands;

use pocketmine\command\CommandSender;
use Legacy\ThePit\Exceptions\LanguageException;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

final class GameModeCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            if ($sender instanceof Player) {
                if (isset($args[0])) {
                    try {
                        $target = $sender;
                        another:
                        if (isset($args[1])) {
                            $target = Server::getInstance()->getPlayerByPrefix($args[1]);
                            if (!$target) {
                                throw new LanguageException("messages.commands.target-not-player", ["{player}" => $args[1]]);
                            }
                        } else if (!$sender instanceof Player) throw new LanguageException("messages.commands.sender-not-player");
                        $gamemode = match ($args[0]) {
                            "0", "s", "survie", "survival" => GameMode::SURVIVAL(),
                            "1", "c", "crea", "creative" => GameMode::CREATIVE(),
                            "2", "a", "adventure" => GameMode::ADVENTURE(),
                            "3", "sp", "spec", "spectator" => GameMode::SPECTATOR(),
                            default => throw new LanguageException("messages.commands.gamemode.invalid-gamemode", ["{gamemode}" => $args[0]])
                        };
                        $target->setGamemode($gamemode);
                        $target->getName() === $sender->getName()
                            ? throw new LanguageException("messages.commands.gamemode.success-self", ["{gamemode}" => $gamemode->getEnglishName()])
                            : throw new LanguageException("messages.commands.gamemode.success-other", ["{player}" => $target->getName(), "{gamemode}" => $gamemode->getEnglishName()]);
                    } catch (LanguageException $e) {
                        $this->getSenderLanguage($sender)->getMessage($e->getMessage(), $e->getArgs(), $e->getPrefix())->send($sender);
                    }
                } else {
                    $sender->sendMessage($this->getUsage());
                }
            } else {
                goto another;
            }
        }
    }
}