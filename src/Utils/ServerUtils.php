<?php

namespace Legacy\ThePit\Utils;

abstract class ServerUtils
{
    const VERSION = "1.0.0";

    const PREFIX_1 = 1;
    const PREFIX_2 = 2;
    const PREFIX_3 = 3;
    const PREFIX_4 = 4;

    public static bool $global_mute = false;

    /**
     * @return bool
     */
    public static function isGlobalMute(): bool
    {
        return self::$global_mute;
    }

    /**
     * @param bool $value
     */
    public static function setGlobalMute(bool $value = true): void
    {
        self::$global_mute = $value;
    }

}