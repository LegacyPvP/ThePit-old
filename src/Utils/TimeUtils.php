<?php

namespace Legacy\ThePit\utils;

use DateTime;
use Exception;

abstract class TimeUtils
{
    public static function strToDate(string $date): ?DateTime
    {
        try {
            $datetime = new DateTime(
                str_replace(
                    [
                        "m", "h", "d", "w"
                    ],
                    [
                        "minutes", "hours", "days", "weeks"
                    ],
                    strtolower($date)
                )
            );
            if ($datetime->getTimestamp() <= time()) {
                throw new Exception("La Date est invalide");
            }
            return $datetime;
        } catch (Exception) {
            return null;
        }
    }
}