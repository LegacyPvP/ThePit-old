<?php
namespace Legacy\ThePit\Currencies;

use Legacy\ThePit\Currencies\Currency;
use Legacy\ThePit\Utils\CurrencyUtils;

final class Credits extends Currency {
    public function __construct()
    {
        parent::__construct(CurrencyUtils::CREDITS);
    }
}