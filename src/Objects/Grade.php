<?php

namespace Legacy\ThePit\Objects;

final class Grade
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
     * @return string
     */
    public function getNametag(): string
    {
        return $this->nametag;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @return string
     */
    public function getScoretag(): string
    {
        return $this->scoretag;
    }

    public function getFormat(array $params = []): string
    {
        $format = $this->getChat();
        foreach ($params as $key => $value) {
            $format = str_replace($key, $value, $format);
        }
        return $format;
    }
}