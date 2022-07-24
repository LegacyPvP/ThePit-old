<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Databases\LanguageDatabase;
use Legacy\ThePit\Objects\Language;

final class LanguageManager extends Managers
{
    /**
     * @var Language[]
     */
    public array $languages = [];

    public function init(): void
    {
        @mkdir(Core::getInstance()->getDataFolder() . "languages");
        $this->saveDefaultConfig();
        foreach ($this->getConfigLanguages() as $language) {
            $this->languages[$language] = new Language($language, new LanguageDatabase($language));
            Core::getInstance()->getLogger()->notice("[LANGUAGES] Lang: $language Loaded");
        }
    }

    /**
     * @return Language[]
     */
    public function getAll(): array
    {
        return $this->languages;
    }

    public static function getConfigLanguages(): array
    {
        $languages = [];
        foreach (scandir(Core::getInstance()->getDataFolder() . "languages") as $file) {
            if ($file === "." or $file === "..") continue;
            $languages[] = str_replace([".yml", "lang_"], ["", ""], $file);
        }
        return $languages;
    }

    public function get(string $name): ?Language
    {
        return $this->languages[$name] ?? reset($this->languages) ?: null;
    }

    public function getPrefix(int $prefix): string
    {
        $prefixes = $this->getPrefixes();
        return $prefixes[$prefix] ?? reset($prefixes) ?? "";
    }

    public function getPrefixes(): array
    {
        return Core::getInstance()->getConfig()->get("prefixes", []);
    }

    public function getDefaultLanguage(): Language
    {
        return reset($this->languages);
    }

    private static function saveDefaultConfig(): void
    {
        $languages = Core::getInstance()->getConfig()->get("languages", []);
        foreach ($languages as $language) {
            Core::getInstance()->saveResource("languages/lang_$language.yml", Core::getInstance()->isInDevMode());
        }
    }
}