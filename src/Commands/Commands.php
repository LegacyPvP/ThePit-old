<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\Core;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\objects\Language;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\traits\CommandTrait;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class Commands extends Command implements PluginOwned
{
    use CommandTrait;

    abstract public function execute(CommandSender $sender, string $commandLabel, array $args): void;

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        self::setCommand($this);
        self::init();
    }

    public function getUsage(): Translatable|string
    {
        return Managers::LANGUAGES()->getPrefix(ServerUtils::PREFIX_3) . parent::getUsage();
    }

    public function getSenderLanguage(CommandSender $sender): ?Language
    {
        return (match ($sender::class) {
            LegacyPlayer::class => $sender->getLanguage(),
            ConsoleCommandSender::class => Managers::LANGUAGES()->getDefaultLanguage(),
            default => null
        });
    }

    public function getOwningPlugin(): Plugin
    {
        return Core::getInstance();
    }

    public function testPermissionSilent(CommandSender $target, ?string $permission = null): bool
    {
        if (!parent::testPermissionSilent($target)) {
            self::getSenderLanguage($target)->getMessage("messages.commands.not-permission")->send($target);
            return false;
        }
        return true;
    }
}