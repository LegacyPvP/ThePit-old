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

final class CommandsManager extends Managers
{
    public function getAll(): array
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

    public function init(): void
    {
        foreach (Core::getInstance()->getServer()->getCommandMap()->getCommands() as $command) {
            foreach ($this->getAll() as $cmd) {
                if ($cmd->getName() === $command->getName()) {
                    Core::getInstance()->getServer()->getCommandMap()->unregister($command);
                }
            }
        }

        foreach ($this->getAll() as $command) {
            Core::getInstance()->getServer()->getCommandMap()->register($command->getName(), $command);
            Core::getInstance()->getLogger()->notice("[COMMANDS] Command: /{$command->getName()} Loaded");
        }
    }

    public function get(string $name): ?object
    {
        return Core::getInstance()->getServer()->getCommandMap()->getCommand($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getDescription(string $name): string
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['description' => ""])['description'] ?? "";
    }

    /**
     * @param string $name
     * @return string
     */
    public function getUsage(string $name): string
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['usage' => "/$name"])['usage'] ?? "";
    }

    /**
     * @param string $name
     * @return array
     */
    public function getAliases(string $name): array
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['aliases' => []])['aliases'] ?? [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getPermission(string $name): string
    {
        return Core::getInstance()->getConfig()->getNested("commands.$name", ['permission' => "core.commands.$name"])['permission'] ?? "core.commands.$name";
    }
}