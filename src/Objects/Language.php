<?php

namespace Legacy\ThePit\objects;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\databases\LanguageDatabase;
use Legacy\ThePit\managers\Managers;

final class Language extends \pocketmine\lang\Language
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(private string $name, private LanguageDatabase $database)
    {

    }

    /**
     * @return string
     */
    #[Pure] public function getLangName(): string
    {
        return $this->getName();
    }


    protected static function loadLang(string $path, string $languageCode): array
    {
        return (new self($languageCode, new LanguageDatabase($languageCode)))->database->getAll();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return LanguageDatabase
     */
    public function getDatabase(): LanguageDatabase
    {
        return $this->database;
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
        return new Language(array_key_first(Managers::LANGUAGES()->languages) ?? "FR", new LanguageDatabase(array_key_first(Managers::LANGUAGES()->languages)) ?? "FR");
    }
}