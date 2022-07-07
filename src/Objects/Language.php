<?php

namespace Legacy\ThePit\Objects;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Databases\LanguageDatabase;
use Legacy\ThePit\Managers\LanguageManager;

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

    public function getMessage(string $key, array $params = [], bool $prefix = true): Message
    {
        $message = ($prefix ? LanguageManager::getPrefix() : "").$this->getDatabase()->getNested($key, $key);
        foreach ($params as $key => $value){
            $message = str_replace($key, $value, $message);
        }
        return new Message($message);
    }

    public static function make(): Language
    {
        return new Language(array_key_first(LanguageManager::$languages) ?? "FR", new LanguageDatabase(array_key_first(LanguageManager::$languages)) ?? "FR");
    }
}