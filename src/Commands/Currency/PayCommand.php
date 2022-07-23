<?php

namespace Legacy\ThePit\Commands\Currency;

use Legacy\ThePit\Commands\Commands;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Objects\Sound;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\CurrencyUtils;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

final class PayCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                if ($sender instanceof LegacyPlayer) {
                    if (isset($args[0], $args[1])) {
                        $target = $sender->getServer()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                        $amount = is_numeric((int)$args[1]) ? (int)$args[1] : throw new LanguageException("messages.commands.pay.invalid-amount", [], ServerUtils::PREFIX_2);
                        if ($sender->getCurrencyProvider()->has(CurrencyUtils::GOLD, $amount)) {
                            if ($target instanceof LegacyPlayer) {
                                (new Sound("random.orb"))->play($sender, $target);
                                $target->getCurrencyProvider()->add(CurrencyUtils::GOLD, $amount);
                                $sender->getCurrencyProvider()->remove(CurrencyUtils::GOLD, $amount);
                                $target->getLanguage()->getMessage("messages.commands.pay.received", [
                                    "{player}" => $sender->getName(),
                                    "{amount}" => $amount
                                ], ServerUtils::PREFIX_3)->send($target);
                                throw new LanguageException("messages.commands.pay.success", [
                                    "{target}" => $target->getName(),
                                    "{amount}" => $amount
                                ], ServerUtils::PREFIX_3);
                            } else throw new LanguageException("messages.commands.target-not-found", [], ServerUtils::PREFIX_2);
                        } else throw new LanguageException("messages.commands.pay.not-enough-gold", ["{amount}" => $amount], ServerUtils::PREFIX_2);
                    } else $sender->sendMessage($this->getUsage());
                } else throw new LanguageException("messages.commands.no-player", [], ServerUtils::PREFIX_2);
            } catch (LanguageException $exception){
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }
        }
    }
}