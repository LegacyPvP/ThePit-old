<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\exceptions\FormsException;
use Legacy\ThePit\exceptions\LanguageException;
use Legacy\ThePit\managers\FormsManager;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\command\CommandSender;

final class EquipmentCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof LegacyPlayer){
                try {
                    Managers::FORMS()->sendForm($sender, "equipment");
                } catch (FormsException $exception) {
                    throw new LanguageException($exception->getMessage(), $exception->getArgs(), $exception->getPrefix(), $exception->getCode(), $exception);
                }
            }
        }
    }
}
