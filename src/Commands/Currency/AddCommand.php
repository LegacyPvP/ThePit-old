<?php

namespace Legacy\ThePit\Commands\Currency;

use Legacy\ThePit\Commands\Commands;
use Legacy\ThePit\Currencies\Currency;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Managers\CurrenciesManager;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\CurrencyUtils;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

final class AddCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                if (isset($args[0], $args[1], $args[2])) {
                    $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                    $amount = is_numeric($args[1]) ? (int)$args[1] : throw new LanguageException("messages.commands.add.invalid-amount", [], ServerUtils::PREFIX_2);
                    $currency = match ($args[2]) {
                        "stars", "étoiles", "etoiles", "star", "étoile", "etoile" => CurrencyUtils::STARS,
                        "gold", "golds", "or" => CurrencyUtils::GOLD,
                        "vote", "votecoins" => CurrencyUtils::VOTECOINS,
                        "crédits", "crédit", "credits", "credit" => CurrencyUtils::CREDITS,
                        default => throw new LanguageException("messages.commands.add.invalid-currency", ["{currency}" => $args[2]], ServerUtils::PREFIX_2)
                    };
                    if ($target instanceof LegacyPlayer) {
                        $target->getCurrencyProvider()->add($currency, $amount);
                        $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.add.success", [
                            "{target}" => $target->getName(),
                            "{currency}" => $currency,
                            "{amount}" => $amount
                        ], ServerUtils::PREFIX_3));
                    } else throw new LanguageException("messages.commands.target-not-found", [], ServerUtils::PREFIX_2);
                } else $sender->sendMessage($this->getUsage());
            } catch (LanguageException $exception) {
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }
        }
    }
}
