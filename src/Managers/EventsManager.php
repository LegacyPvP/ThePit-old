<?php

namespace Legacy\ThePit\Managers;

abstract class EventsManager
{
    public const TYPE_NONE = "none";
    public const TYPE_DEATHMATCH = "deathmatch";
    public const TYPE_RAFFLE = "raffle";
    public const TYPE_SPIRE = "spire";

    public static function initEvents(): void
    {

    }

    public static function getEvents(): array
    {
        return [];
    }

    public static function getCurrentEvent(): string
    {
        return self::TYPE_NONE;
    }

}