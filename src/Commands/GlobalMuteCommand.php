<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;

final class GlobalMuteCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            $sender_language = $this->getSenderLanguage($sender);
            $mode = match ($args[0] ?? 'on') {
                'o', 'y', 'on', 'true', 'oui', 'yes' => true,
                'n', 'x', 'off', 'false', 'non', 'no' => false,
                default => null
            };
            if (is_bool((bool)$mode) and !is_null($mode)) {
                ServerUtils::setGlobalMute($mode);
                if (!ServerUtils::$global_mute) {
                    $sender_language->getMessage("messages.commands.globalmute.unmuted")->send($sender);
                } else {
                    $sender_language->getMessage("messages.commands.globalmute.muted")->send($sender);
                }
            } else {
                $sender->sendMessage($this->getUsage());
            }
        }
    }
}
