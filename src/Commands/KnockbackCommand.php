<?php

namespace Legacy\ThePit\Commands;

use JsonException;
use Legacy\ThePit\Core;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

final class KnockbackCommand extends Commands
{
    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if(isset($args[0], $args[1])){
                try {
                    $force = $args[0];
                    $vertical_limit = $args[1];
                    $attack_cooldown = $args[2] ?? 10;
                    if(is_numeric($force) and is_numeric($vertical_limit) and is_numeric($attack_cooldown)){
                        $config = Core::getInstance()->getConfig();
                        $config->setNested("knockback.horizontal", (int)$force);
                        $config->setNested("knockback.vertical", (int)$vertical_limit);
                        $config->setNested("knockback.attack_cooldown", (int)$attack_cooldown);
                        $config->save();
                        throw new LanguageException("messages.commands.knockback.success", [
                            "{horizontal}" => $force,
                            "{vertical}" => $vertical_limit,
                            "{attack_cooldown}" => $attack_cooldown
                        ]);
                    }else{
                        throw new LanguageException("messages.commands.knockback.invalid-arguments", [], ServerUtils::PREFIX_2);
                    }
                }
                catch (LanguageException $exception){
                    $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
                }
            } else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }

}