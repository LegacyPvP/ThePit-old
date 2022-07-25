<?php

namespace Legacy\ThePit\managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\currencies\Credits;
use Legacy\ThePit\currencies\Etoiles;
use Legacy\ThePit\currencies\Gold;
use Legacy\ThePit\currencies\VoteCoins;
use Legacy\ThePit\currencies\Currency;

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
            Core::getInstance()->getLogger()->notice("[CURRENCIES] currency: {$currency->getName()} Loaded");
        }
    }

    public function get(string $name): ?Currency
    {
        return $this->currencies[$name] ?? reset($this->currencies) ?: null;
    }
}
