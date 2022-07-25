<?php

namespace Legacy\ThePit\traits;

trait CacheTrait
{
    private static array $cache = [];

    public static function getCache(): array {
        return self::$cache;
    }

    public static function getInCache(string $key, $default = null)
    {
        return self::$cache[$key] ?? $default;
    }

    public static function setInCache(string $key, $value)
    {
        self::$cache[$key] = $value;
    }
}