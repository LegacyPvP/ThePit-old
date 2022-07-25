<?php

namespace Legacy\ThePit\utils;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\forms\Form;
use Legacy\ThePit\player\LegacyPlayer;
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
     * @param array $form_infos
     * @internal
     */
    public static function sendForm(LegacyPlayer $player, array $form_infos): void
    {
        if (!self::getOpened($player)) {
            $form = $form_infos["form"];
            switch ($form_infos["type"]) {
                case Form::TYPE_SIMPLE_FORM:
                    $form->setCloseListener(reset($form_infos["callable"]) ?? null);
                    foreach ($form_infos["buttons"] as $button){
                        $form->addButton($button);
                    }
                    break;
                case Form::TYPE_CUSTOM_FORM:
                    $form->setSubmitListener(reset($form_infos["callable"]) ?? null);
                    $form->setCloseListener(end($form_infos["callable"]) ?? null);
                    break;
                case Form::TYPE_MODAL_FORM:
                    $form->setAcceptListener(reset($form_infos["callable"]) ?? null);
                    $form->setDenyListener(end($form_infos["callable"]) ?? null);
                    break;
            }
            $player->sendForm($form);
            self::setOpened($player);
            Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(fn() => self::unsetOpened($player)), 5);
        }
    }
}