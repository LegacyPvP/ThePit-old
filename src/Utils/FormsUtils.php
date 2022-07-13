<?php

namespace Legacy\ThePit\Utils;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\form\Form;
use pocketmine\scheduler\ClosureTask;

abstract class FormsUtils
{
    /**
     * @var bool[]
     */
    private static array $opened = [];

    /**
     * @param LegacyPlayer $player
     * @return bool
     */
    #[Pure] public static function getOpened(LegacyPlayer $player): bool
    {
        return self::$opened[$player->getName()] ?? false;
    }

    /**
     * @param LegacyPlayer $player
     */
    public static function setOpened(LegacyPlayer $player): void
    {
        self::$opened[$player->getName()] = true;
    }

    /**
     * @param LegacyPlayer $player
     */
    public static function unsetOpened(LegacyPlayer $player): void
    {
        self::$opened[$player->getName()] = false;
    }

    /**
     * @param LegacyPlayer $player
     * @param Form $form
     */
    public static function sendForm(LegacyPlayer $player, Form $form): void {
        if(!self::getOpened($player)){
            $player->sendForm($form);
            self::setOpened($player);
            Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(fn() => self::unsetOpened($player)), 5);
        }
    }
}