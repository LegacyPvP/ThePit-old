<?php

namespace Legacy\ThePit\Objects;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Player\LegacyPlayer;

final class Rank
{
    public function __construct(private string $name, private array $permissions, private string $chat = "", private string $nametag = "", private string $scoretag = "")
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getChat(): string
    {
        return $this->chat;
    }

    /**
     * @param LegacyPlayer $player
     * @return string
     */
    public function getNametag(LegacyPlayer $player): string
    {
        return $this->applyParameters($this->nametag, $player);
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param LegacyPlayer $player
     * @return string
     */
    public function getScoretag(LegacyPlayer $player): string
    {
        return $this->applyParameters($this->scoretag, $player);
    }

    public function getFormat(array $params = []): string {
        $format = $this->getChat();
        foreach ($params as $key => $value){
            $format = str_replace($key, $value, $format);
        }
        return $format;
    }

    private function applyParameters(string $value, LegacyPlayer $player): string
    {
        foreach (RanksManager::getParameters($player) as $key => $param){
            $value = str_replace($key, $param, $value);
        }
        return $value;
    }
}