<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\Objects\Money;
use Legacy\ThePit\Player\LegacyPlayer;

abstract class MoneyManager {

    private static array $currencies = [];

    #[Pure] public static function getCurrencies(): array {
        return [
            new Money("or"),
            new Money("etoiles"),
            new Money("credits"),
            new Money("votecoins", true),
        ];
    }

    public static function initCurrencies(): void {
        foreach (self::getCurrencies() as $name){
            self::$currencies[$name] = new Money($name);
            Core::getInstance()->getLogger()->notice("[MONEY] Currency: $name Loaded");
        }
    }

    #[Pure] public static function getCurrency(string $name): Money {
        return self::$currencies[$name] ?? reset(self::$currencies);
    }
}
