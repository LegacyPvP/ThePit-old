<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\forms\element\Button;
use Legacy\ThePit\forms\variant\SimpleForm;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

final class KeyCrateCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        if (!isset($args[0])) {
            $sender->sendMessage($this->getUsage());
            return;
        }
        switch ($args[0]) {
            case "add":
                if (!isset($args[1])) {
                    $sender->sendMessage("key add <player> <key> <count>");
                    return;
                }
                $player = Server::getInstance()->getPlayerExact($args[1]);
                if (!$player instanceof LegacyPlayer) {
                    $sender->sendMessage($args[1] . " is not connected or doesn't exist at all.");
                    return;
                }
                if (!isset($args[2])) {
                    $sender->sendMessage("key add <player> <key> <count>");
                    return;
                }
                if (!isset($args[3])) {
                    $sender->sendMessage("key add <player> <key> <count>");
                    return;
                }
                if (!in_array($args[2], array_keys(Managers::CRATES()->getAll()), true)) {
                    $sender->sendMessage($args[2] . " doesn't exist at all.");
                    return;
                }

                $player->getPlayerProperties()->setNestedProperties("keys." . $args[2], (int)$player->getPlayerProperties()->getNestedProperties("keys." . $args[2]) + $args[3]);
                break;
            case "remove":
                if (!isset($args[1])) {
                    $sender->sendMessage("key remove <player> <key> <count>");
                    return;
                }
                $player = Server::getInstance()->getPlayerExact($args[1]);
                if (!$player instanceof LegacyPlayer) {
                    $sender->sendMessage($args[1] . " is not connected or doesn't exist at all.");
                    return;
                }
                if (!isset($args[2])) {
                    $sender->sendMessage("key remove <player> <key> <count>");
                    return;
                }
                if (!isset($args[3])) {
                    $sender->sendMessage("key remove <player> <key> <count>");
                    return;
                }
                if (!in_array($args[2], array_keys(Managers::CRATES()->getAll()), true)) {
                    $sender->sendMessage($args[2] . " doesn't exist at all.");
                    return;
                }
                if (((int)$player->getPlayerProperties()->getNestedProperties("keys." . $args[2]) - $args[3]) <= 0) {
                    $player->getPlayerProperties()->setNestedProperties("keys." . $args[2], (int)$player->getPlayerProperties()->getNestedProperties("keys." . $args[2]) - (int)$player->getPlayerProperties()->getNestedProperties("keys." . $args[2]));
                    return;
                }
                $player->getPlayerProperties()->setNestedProperties("keys." . $args[2], (int)$player->getPlayerProperties()->getNestedProperties("keys." . $args[2]) - $args[3]);
                break;
            case "get":
                if (!isset($args[2])) {
                    $sender->sendMessage("key get <player>");
                    return;
                }
                $player = Server::getInstance()->getPlayerExact($args[2]);
                if (!$player instanceof LegacyPlayer) {
                    $sender->sendMessage($args[2] . " is not connected or doesn't exist at all.");
                    return;
                }
                if (empty($player->getPlayerProperties()->getProperties("keys")) or empty(array_values($player->getPlayerProperties()->getProperties("keys")))) {
                    $sender->sendMessage("$player has no key.");
                    return;
                }
                foreach ($player->getPlayerProperties()->getProperties("keys") as $name => $count) {
                    $sender->sendMessage("$player has x$count $name");
                }
                break;
            default:
                foreach (Managers::CRATES()->getAll() as $crate) {
                    $sender->sendMessage($crate);
                }
                break;
        }
    }

    public function getUsage(): string
    {
        return "key add|remove|get|list";
    }
}