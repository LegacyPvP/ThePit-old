<?php

namespace Legacy\ThePit\Utils;

abstract class ServerUtils
{
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