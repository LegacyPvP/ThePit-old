<?php

namespace Legacy\ThePit\currencies;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\utils\CurrencyUtils;

final class Credits extends Currency
{
    #[Pure] public function __construct()
    {
        parent::__construct(CurrencyUtils::CREDITS);
    }
}