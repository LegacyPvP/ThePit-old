<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
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
