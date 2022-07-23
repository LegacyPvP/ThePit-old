<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Commands\BanCommand;
use Legacy\ThePit\Commands\BanListCommand;
use Legacy\ThePit\Commands\Currency\{
    AddCommand,
    PayCommand,
    RemoveCommand,
    SetCommand
};
use Legacy\ThePit\Commands\{
    GameModeCommand,
    GlobalMuteCommand,
    KickCommand,
    KnockBackCommand,
    ListCommand,
    ListRankCommand,
    LobbyCommand,
    MessageCommand,
    MuteCommand,
    NightVisionCommand,
    PingCommand,
    SayCommand,
    SetRankCommand,
    SpawnCommand,
    StatisticsCommand,
    TpCommand,
    TprCommand,
    TPSCommand,
    UnbanCommand,
    UnmuteCommand,
};
use Legacy\ThePit\Core;

abstract class CommandsManager
{
    public static function getCommands(): array
    {
        return [
            new BanCommand('ban'),
            new UnbanCommand('unban'),
            new KickCommand('kick'),
            new SetRankCommand('setrank'),
            new ListRankCommand('listrank'),
            new BanListCommand('banlist'),
            new GameModeCommand('gamemode'),
            new SayCommand("say"),
            new GlobalMuteCommand("globalmute"),
            new TprCommand("tpr"),
            new TpCommand("tp"),
            new KnockBackCommand("knockback"),
            new PingCommand("ping"),
            new NightVisionCommand("nightvision"),
            new SpawnCommand("spawn"),
            new LobbyCommand("lobby"),
            new MuteCommand("mute"),
            new UnmuteCommand("unmute"),
            new TPSCommand("tps"),
            new MessageCommand("msg"),
            new PayCommand("pay"),
            new ListCommand("list"),
            new SetCommand("set"),
            new RemoveCommand("remove"),
            new AddCommand("add"),
            new StatisticsCommand("statistics"),
        ];
    }

    public static function initCommands(): void
    {
        foreach (Core::getInstance()->getServer()->getCommandMap()->getCommands() as $command) {
            foreach (self::getCommands() as $cmd) {
                if ($cmd->getName() === $command->getName()) {
                    Core::getInstance()->getServer()->getCommandMap()->unregister($command);
                }
            }
        }

        foreach (self::getCommands() as $command) {
            Core::getInstance()->getServer()->getCommandMap()->register($command->getName(), $command);
            Core::getInstance()->getLogger()->notice("[COMMANDS] Command: /{$command->getName()} Loaded");
        }
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getDescription(string $name): string
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['description' => ""])['description'] ?? "";
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getUsage(string $name): string
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['usage' => "/$name"])['usage'] ?? "";
    }

    /**
     * @param string $name
     * @return array
     */
    public static function getAliases(string $name): array
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['aliases' => []])['aliases'] ?? [];
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getPermission(string $name): string
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['permission' => "core.commands.$name"])['permission'] ?? "core.commands.$name";
    }
}