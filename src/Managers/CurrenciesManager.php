<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\Currencies\Credits;
use Legacy\ThePit\Currencies\Etoiles;
use Legacy\ThePit\Currencies\Gold;
use Legacy\ThePit\Currencies\VoteCoins;
use Legacy\ThePit\Currencies\Currency;

final class CurrenciesManager extends Managers
{

    /**
     * @var Currency[]
     */
    private array $currencies = [];

    /**
     * @return Currency[]
     */
    #[Pure] public function getAll(): array
    {
        return [
            new Gold(),
            new Credits(),
            new VoteCoins(),
            new Etoiles()
        ];
    }

    public function init(): void
    {
        foreach ($this->getAll() as $currency) {
            $this->currencies[$currency->getName()] = $currency;
            Core::getInstance()->getLogger()->notice("[CURRENCIES] Currency: {$currency->getName()} Loaded");
        }
    }

    public function get(string $name): ?Currency
    {
        return $this->currencies[$name] ?? reset($this->currencies) ?: null;
    }
}
