<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Currencies\Credits;
use Legacy\ThePit\Currencies\Etoiles;
use Legacy\ThePit\Currencies\Gold;
use Legacy\ThePit\Currencies\VoteCoins;
use Legacy\ThePit\Currencies\Currency;

abstract class CurrenciesManager {

    /**
     * @var Currency[]
     */
    private static array $currencies = [];

    /**
     * @return Currency[]
     */
    public static function getCurrencies(): array {
        return [
            new Gold(),
            new Credits(),
            new VoteCoins(),
            new Etoiles()
        ];
    }

    public static function initCurrencies(): void {
        foreach (self::getCurrencies() as $currency){
            self::$currencies[$currency->getName()] = $currency;
            Core::getInstance()->getLogger()->notice("[CURRENCIES] Currency: {$currency->getName()} Loaded");
        }
    }

    public static function getCurrency(string $name): Currency {
        return self::$currencies[$name] ?? reset(self::$currencies);
    }
}
