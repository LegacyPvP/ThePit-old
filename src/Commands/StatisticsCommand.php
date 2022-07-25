<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\CurrencyUtils;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\OfflinePlayer;
use pocketmine\Server;

final class StatisticsCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            try {
                if ($sender instanceof LegacyPlayer) {
                    if (!isset($args[0])) throw new LanguageException("messages.commands.statistics.success", [
                        "{player}" => $sender->getName(),
                        "{stars}" => $sender->getCurrencyProvider()->get(CurrencyUtils::STARS),
                        "{gold}" => $sender->getCurrencyProvider()->get(CurrencyUtils::GOLD),
                        "{votecoins}" => $sender->getCurrencyProvider()->get(CurrencyUtils::VOTECOINS),
                        "{credits}" => $sender->getCurrencyProvider()->get(CurrencyUtils::CREDITS),
                    ], ServerUtils::PREFIX_4);
                    else {
                        $target = $sender->getServer()->getPlayerByPrefix($args[0]) ?? $sender->getServer()->getOfflinePlayer($args[0]);
                        if ($target instanceof LegacyPlayer) throw new LanguageException("messages.commands.statistics.success", [
                            "{player}" => $target->getName(),
                            "{stars}" => $target->getCurrencyProvider()->get(CurrencyUtils::STARS),
                            "{gold}" => $target->getCurrencyProvider()->get(CurrencyUtils::GOLD),
                            "{votecoins}" => $target->getCurrencyProvider()->get(CurrencyUtils::VOTECOINS),
                            "{credits}" => $target->getCurrencyProvider()->get(CurrencyUtils::CREDITS),
                        ], ServerUtils::PREFIX_4);
                        elseif ($target instanceof OfflinePlayer and
                            ($data = Server::getInstance()->getOfflinePlayerData($target->getName())) and
                            ($properties = $data->getCompoundTag('properties')->getCompoundTag('money')))
                            throw new LanguageException("messages.commands.statistics.success", [
                                "{player}" => $target->getName(),
                                "{stars}" => $properties->getTag(CurrencyUtils::STARS)->getValue() ?? 0,
                                "{gold}" => $properties->getTag(CurrencyUtils::GOLD)->getValue() ?? 0,
                                "{votecoins}" => $properties->getTag(CurrencyUtils::VOTECOINS)->getValue() ?? 0,
                                "{credits}" => $properties->getTag(CurrencyUtils::CREDITS)->getValue() ?? 0,
                            ], ServerUtils::PREFIX_4);
                        else throw new LanguageException("messages.commands.target-not-player", [], ServerUtils::PREFIX_2);
                    }
                } else throw new LanguageException("messages.commands.no-player", [], ServerUtils::PREFIX_4);
            } catch (LanguageException $exception) {
                $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
            }
        }
    }
}