<?php

namespace Legacy\ThePit\Currencies;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Utils\CurrencyUtils;

final class Etoiles extends Currency
{
    #[Pure] public function __construct()
    {
        parent::__construct(CurrencyUtils::STARS);
    }
}