<?php

namespace Legacy\ThePit\Objects;

use Legacy\ThePit\Databases\IDatabase;
use Legacy\ThePit\Managers\Managers;

final class Language extends \pocketmine\lang\Language
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(private string $name)
    {

    }

    protected static function loadLang(string $path, string $languageCode): array
    {
        return (new self($languageCode))->getDatabase()->dump();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ?IDatabase
     */
    public function getDatabase(): ?IDatabase
    {
        return Managers::DATA()->get($this->name);
    }

    public function getMessage(string $key, array $params = [], int $prefix = 1): Message
    {
        $message = ($prefix ? Managers::LANGUAGES()->getPrefix($prefix) : "") . $this->getDatabase()->getNested($key, $key);
        foreach ($params as $key => $value) {
            $message = str_replace($key, $value, $message);
        }
        return new Message($message);
    }

    public static function make(): Language
    {
        return new Language(array_key_first(Managers::LANGUAGES()->languages) ?? "fr_FR");
    }
}