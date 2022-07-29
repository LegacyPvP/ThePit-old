<?php

namespace Legacy\ThePit\utils;

abstract class CurrencyUtils
{
    public const STARS = "etoiles";
    public const GOLD = "gold";
    public const VOTECOINS = "votecoins";
    public const CREDITS = "credits";

    public static function getText(int $amount): string
    {
        switch ($amount){
            case $amount <= 99999:
                return round($amount /1000, 1) . "K";
            case $amount <= 999999:
                return round($amount/1000000, 1) . "M";
            case $amount <= 9999999:
                return round($amount / 10000000, 1) . "MA";
            default:
                return $amount;
        }
    }

}