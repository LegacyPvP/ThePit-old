<?php

namespace Legacy\ThePit\traits;

use Legacy\ThePit\managers\Managers;
use pocketmine\command\Command;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;

trait CommandTrait
{
    /**
     * @var Command|null
     */
    private static ?Command $command = null;

    /**
     * @param Command $command
     */
    private static function setCommand(Command $command): void
    {
        self::$command = $command;
    }

    public static function init()
    {
        if (self::$command !== null) {
            self::$command->setDescription(Managers::COMMANDS()->getDescription(self::$command->getName()));
            self::$command->setAliases(Managers::COMMANDS()->getAliases(self::$command->getName()));
            self::$command->setUsage(Managers::COMMANDS()->getUsage(self::$command->getName()));
            self::$command->setPermission(
                PermissionManager::getInstance()->addPermission(new Permission(Managers::COMMANDS()->getPermission(self::$command->getName())))
                    ? Managers::COMMANDS()->getPermission(self::$command->getName())
                    : DefaultPermissions::ROOT_OPERATOR
            );
        }
    }
}