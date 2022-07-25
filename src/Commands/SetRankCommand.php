<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\OfflinePlayer;
use pocketmine\Server;

final class SetRankCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            if (isset($args[0], $args[1])) {
                $rank = Managers::RANKS()->get($args[1]);
                if ($rank) {
                    $target = Server::getInstance()->getPlayerByPrefix($args[0]) ?? Server::getInstance()->getOfflinePlayer($args[0]);
                    if ($target instanceof LegacyPlayer) {
                        $target->getPlayerProperties()->setNestedProperties("infos.rank", $rank->getName());
                        $this->getSenderLanguage($sender)->getMessage("messages.commands.setrank.success", ["{player}" => $target->getName(), "{rank}" => $rank->getName()])->send($sender);
                    } else if ($target instanceof OfflinePlayer and Server::getInstance()->getOfflinePlayerData($target->getName())) {
                        $properties = Server::getInstance()->getOfflinePlayerData($target->getName())->getCompoundTag('properties');
                        $properties ??= (new CompoundTag());
                        $nbt = (new CompoundTag())->merge($properties);
                        $nbt->setTag("infos", (new CompoundTag())->setTag("rank", new StringTag($rank->getName())));
                        $properties->setTag("properties", $nbt);
                        Server::getInstance()->saveOfflinePlayerData($target->getName(), $properties);
                        $this->getSenderLanguage($sender)->getMessage("messages.commands.setrank.success", ["{player}" => $target->getName(), "{rank}" => $rank->getName()])->send($sender);
                    } else {
                        $this->getSenderLanguage($sender)->getMessage("messages.commands.target-not-player")->send($sender);
                    }
                } else {
                    $this->getSenderLanguage($sender)->getMessage("messages.commands.setrank.invalid-rank", ["{rank}" => $args[1]])->send($sender);
                }
            } else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}