<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

final class KnockbackCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if(isset($args[0])){
                $x = $args[0];
                if(isset($args[1])){
                    $y = $args[1];
                    if(isset ($args[2])){
                        $force = $args[2];
                        if(isset($args[3])){
                            $vertical_limit = $args[3];
                            if(isset($args[4])){
                                $attack_cooldown = $args[4];
                                Core::getInstance()->getConfig()->setNested("knockback", [
                                    "x" => $x,
                                    "y" => $y,
                                    "force" => $force,
                                    "vertical_limit" => $vertical_limit,
                                    "attack_cooldown" => $attack_cooldown
                                ]);
                                $sender_language->getMessage("messages.commands.knockback.success", [
                                    "{x}" => $x,
                                    "{y}" => $y,
                                    "{force}" => $force,
                                    "{vertical-limit}" => $vertical_limit,
                                    "{attack-cooldown}" => $attack_cooldown
                                ])->send($sender);
                            }else{
                                $sender->sendMessage($this->getUsage());
                            }
                        }else{
                            $sender->sendMessage($this->getUsage());
                        }
                    }else{
                        $sender->sendMessage($this->getUsage());
                    }
                }else{
                    $sender->sendMessage($this->getUsage());
                }
            }else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }

}