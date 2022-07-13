<?php
namespace Legacy\ThePit\Currencies;

use Legacy\ThePit\Currencies\Currency;

final class Credits extends Currency {
    public function __construct()
    {
        parent::__construct("credits");
    }
}