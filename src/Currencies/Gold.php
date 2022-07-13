<?php
namespace Legacy\ThePit\Currencies;

use Legacy\ThePit\Currencies\Currency;

final class Gold extends Currency
{
    public function __construct()
    {
        parent::__construct("gold");
    }
}