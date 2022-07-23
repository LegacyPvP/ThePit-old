<?php

namespace Legacy\ThePit\Commands;

use pocketmine\command\CommandSender;

final class FreezeCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            $sender_language = $this->getSenderLanguage($sender);
            if (isset($args[0])) {
                $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                if ($target->isOnline()) {
                    if (!$target->isImmobile()) {
                        $target->setImmobile();
                        $target->getLanguage()->getMessage("messages.commands.freeze.target-freeze")->send($sender);
                        $sender_language->getMessage("messages.commands.freeze.freeze-success")->send($sender);
                    } else {
                        $target->setImmobile(false);
                        $target->getLanguage()->getMessage("messages.commands.freeze.target-unfreeze-success")->send($sender);
                        $sender_language->getMessage("messages.commands.freeze.unfreeze-success")->send($sender);
                    }
                }
            }
        }
    }
}
