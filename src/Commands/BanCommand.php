<?php

namespace Legacy\ThePit\Commands;

use Exception;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Utils\TimeUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use pocketmine\Server;

final class BanCommand extends Commands
{
    /**
     * @throws Exception
     * @noinspection PhpMissingBreakStatementInspection
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if(isset($args[0], $args[1])){
                try {
                    $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                    if($target instanceof IPlayer){
                        $time = strtolower($args[1]) === "perm" ? null : TimeUtils::strToDate($args[1]);
                        $ip = $args[2] ?? "non";
                        $reason = isset($args[3]) ? implode(" ", array_slice($args, 3)) : "Aucune raison donnée.";
                        switch (strtolower($ip)){
                            case "o":
                            case "oui":
                                if($target instanceof Player) Server::getInstance()->getIPBans()->addBan($target->getNetworkSession()->getIp(), $reason, $time);
                            case "n":
                            case "non":
                            default:
                                Server::getInstance()->getNameBans()->addBan($target->getName(), $reason, $time);
                                if($target instanceof Player) $target->kick(
                                    $target->getLanguage()->getMessage("messages.commands.ban.banned",
                                        [
                                            "{player}" => $sender->getName(),
                                            "{date}" => $time?->format("d/m/Y H:i:s") ?? "Permanent",
                                            "{reason}" => $reason
                                        ]
                                    )
                                );
                                throw new LanguageException("messages.commands.ban.success", ["{player}" => $target->getName(), "{date}" => $time?->format("d/m/Y H:i:s") ?? "Permanent", "{reason}" => $reason]);
                        }
                    }
                    else {
                        throw new LanguageException("messages.commands.target-not-player");
                    }
                }
                catch (LanguageException $exception){
                    $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
                }
            }
            else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }

}