<?php

namespace Legacy\ThePit\Commands;

use pocketmine\command\CommandSender;
use pocketmine\Server;

final class BanListCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($this->testPermissionSilent($sender)){
            $list = $this->getSenderLanguage($sender)->getMessage("messages.commands.banlist.header")->__toString();
            foreach(Server::getInstance()->getNameBans()->getEntries() as $banEntry){
                $list .= str_replace(["{name}", "{date}", "{reason}"], [$banEntry->getName(), $banEntry->getExpires()?->format("d/m/Y H:i:s") ?? "Permanent", $banEntry->getReason()], $this->getSenderLanguage($sender)->getMessage("messages.commands.banlist.format", [], false)->__toString());
            }
            $sender->sendMessage($list);
        }
    }
}