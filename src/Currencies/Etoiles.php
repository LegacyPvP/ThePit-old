<?php
namespace Legacy\ThePit\Currencies;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Currencies\Currency;

final class Etoiles extends Currency
{
    #[Pure] public function __construct()
    {
        parent::__construct("etoiles");
    }
}