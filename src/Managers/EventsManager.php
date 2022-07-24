<?php

namespace Legacy\ThePit\Managers;

final class EventsManager extends Managers
{
    public const TYPE_NONE = "none";
    public const TYPE_DEATHMATCH = "deathmatch";
    public const TYPE_RAFFLE = "raffle";
    public const TYPE_SPIRE = "spire";

    public static function getCurrentEvent(): string
    {
        return self::TYPE_NONE;
    }

}