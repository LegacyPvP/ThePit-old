<?php
namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Objects\Language;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Traits\CommandTrait;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class Commands extends Command implements PluginOwned
{
    use CommandTrait;

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        self::setCommand($this);
        self::init();
    }

    public function getUsage(): Translatable|string
    {
        return LanguageManager::getPrefix().parent::getUsage();
    }

    public function getSenderLanguage(CommandSender $sender): ?Language {
        return (match($sender::class){
            LegacyPlayer::class => $sender->getLanguage(),
            ConsoleCommandSender::class => LanguageManager::getDefaultLanguage(),
            default => null
        });
    }

    public function getOwningPlugin(): Plugin
    {
        return Core::getInstance();
    }

    public function testPermissionSilent(CommandSender $target, ?string $permission = null): bool
    {
        if(!parent::testPermissionSilent($target)){
            self::getSenderLanguage($target)->getMessage("messages.commands.not-permission")->send($target);
            return false;
        }
        return true;
    }
}