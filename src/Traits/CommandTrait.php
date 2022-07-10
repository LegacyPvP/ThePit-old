<?php

namespace Legacy\ThePit\Traits;

use Legacy\ThePit\Managers\CommandsManager;
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
    private static function setCommand(Command $command): void {
        self::$command = $command;
    }

    public static function init(){
        if(self::$command !== null){
            self::$command->setDescription(CommandsManager::getDescription(self::$command->getName()));
            self::$command->setAliases(CommandsManager::getAliases(self::$command->getName()));
            self::$command->setUsage(CommandsManager::getUsage(self::$command->getName()));
            self::$command->setPermission(
                PermissionManager::getInstance()->addPermission(new Permission(CommandsManager::getPermission(self::$command->getName())))
                    ? CommandsManager::getPermission(self::$command->getName())
                    : DefaultPermissions::ROOT_OPERATOR
            );
        }
    }
}