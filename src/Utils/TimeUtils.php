<?php

namespace Legacy\ThePit\Utils;

use DateTime;
use Exception;

abstract class TimeUtils
{
    public static function strToDate(string $date): ?DateTime
    {
        try {
            $datetime = new DateTime(
                strtotime(
                    str_replace(
                        [
                            "s", "m", "h", "d", "w", "y"
                        ],
                        [
                            "seconds", "minutes", "hours", "days", "weeks", "years"
                        ],
                        strtolower($date)
                    )
                )
            );
            if($datetime->getTimestamp() <= time()) throw new Exception("La Date est invalide");
            return $datetime;
        } catch (Exception) {
            return null;
        }
    }
}