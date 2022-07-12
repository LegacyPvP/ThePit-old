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
                                if(is_numeric($x) and is_numeric($y) and is_numeric($force) and is_numeric($vertical_limit) and is_numeric($attack_cooldown)){
                                    $config = Core::getInstance()->getConfigByName("knockback");
                                    $config->set("x", (int)$x);
                                    $config->set("y", (int)$y);
                                    $config->set("force", (int)$force);
                                    $config->set("vertical_limit", (int)$vertical_limit);
                                    $config->set("attack_cooldown", (int)$attack_cooldown);
                                    $config->save();
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
            }else{
                $sender->sendMessage($this->getUsage());
            }
        }
    }

}