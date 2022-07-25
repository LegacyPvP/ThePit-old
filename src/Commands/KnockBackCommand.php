<?php

namespace Legacy\ThePit\commands;

use JsonException;
use Legacy\ThePit\exceptions\FormsException;
use Legacy\ThePit\exceptions\LanguageException;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\command\CommandSender;

final class KnockBackCommand extends Commands
{
    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                if ($sender instanceof LegacyPlayer) {
                    try {
                        Managers::FORMS()->sendForm($sender, "knockback");
                    } catch (FormsException $exception) {
                        throw new LanguageException($exception->getMessage(), $exception->getArgs(), $exception->getPrefix(), $exception->getCode(), $exception);
                    }
                } else {
                    if (isset($args[0], $args[1])) {
                        $force = $args[0];
                        $vertical_limit = $args[1];
                        $attack_cooldown = $args[2] ?? 10;
                        if (is_numeric($force) and is_numeric($vertical_limit) and is_numeric($attack_cooldown)) {
                            $config = Managers::DATA()->get("config");
                            $config->setNested("knockback.horizontal", (int)$force);
                            $config->setNested("knockback.vertical", (int)$vertical_limit);
                            $config->setNested("knockback.attack_cooldown", (int)$attack_cooldown);
                            throw new LanguageException("messages.commands.knockback.success", [
                                "{horizontal}" => $force,
                                "{vertical}" => $vertical_limit,
                                "{attack_cooldown}" => $attack_cooldown
                            ]);
                        } else {
                            throw new LanguageException("messages.commands.knockback.invalid-arguments", [], ServerUtils::PREFIX_2);
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                }
            } catch (LanguageException $exception) {
                exception:
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }

        }
    }

}