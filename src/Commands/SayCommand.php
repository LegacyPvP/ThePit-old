<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Managers\Managers;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

final class SayCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            $sender_language = $this->getSenderLanguage($sender);
            if (isset($args[0])) {
                if (str_word_count($args[0]) >= 1) {
                    $message = implode(" ", array_slice($args, 0));
                    $sender_language->getMessage("messages.commands.say.success")->send($sender);
                    $sender->getServer()->broadcastMessage(Managers::LANGUAGES()->getPrefix(ServerUtils::PREFIX_2) . $message);
                } else {
                    $sender_language->getMessage("messages.commands.say.no-message-valid")->send($sender);
                }
            } else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}