<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

final class TPSCommand extends Commands
{

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        try {
            $server = Core::getInstance()->getServer();
            $ticks = match (true) {
                $server->getTicksPerSecond() < 12 => TextFormat::RED . $server->getTicksPerSecond(),
                $server->getTicksPerSecond() < 17 => TextFormat::GOLD . $server->getTicksPerSecond(),
                default => TextFormat::GREEN . $server->getTicksPerSecond(),
            };
            throw new LanguageException("messages.commands.tps.success", [
                "{ticks}" => $ticks,
                "{ticks_per_seconds}" => $server->getTicksPerSecond(),
                "{ticks_usage}" => $server->getTickUsage(),
                "{ticks_per_seconds_average}" => $server->getTicksPerSecondAverage(),
                "{ticks_usage_average}" => $server->getTickUsageAverage(),
            ], ServerUtils::PREFIX_3);
        } catch (LanguageException $exception) {
            $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
        }
    }
}
