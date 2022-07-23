<?php
namespace Legacy\ThePit\Currencies;

use Legacy\ThePit\Currencies\Currency;
use Legacy\ThePit\Utils\CurrencyUtils;

final class VoteCoins extends Currency
{
    public function __construct()
    {
        parent::__construct(CurrencyUtils::VOTECOINS);
    }



}