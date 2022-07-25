<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\exceptions\LanguageException;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\command\CommandSender;

final class NightVisionCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                if ($sender instanceof LegacyPlayer) {
                    if ($sender->getPlayerProperties()->getNestedProperties('status.nightvision')) {
                        $sender->getPlayerProperties()->setNestedProperties('status.nightvision', false);
                        throw new LanguageException("messages.commands.nightvision.success-off");
                    } else {
                        $sender->getPlayerProperties()->setNestedProperties('status.nightvision', true);
                        throw new LanguageException("messages.commands.nightvision.success-on");
                    }
                } else throw new LanguageException("messages.commands.sender-not-player", [], ServerUtils::PREFIX_2);
            } catch (LanguageException $exception) {
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }
        }
    }
}
