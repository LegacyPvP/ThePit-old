<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\databases\LanguageDatabase;
use Legacy\ThePit\objects\Language;

final class LanguageManager extends Managers
{
    /**
     * @var Language[]
     */
    public array $languages = [];

    public function load(): void
    {
        @mkdir(Core::getInstance()->getDataFolder() . "languages");
        foreach ($this->getDataLanguages() as $language) {
            Core::$cache["data"][$language] = new LanguageDatabase("lang_".$language);
            Core::$cache["languages"][$language] = new Language($language);
            // TODO: Managers::DATA()->add(new LanguageDatabase("lang_".$language)); DOESN'T WORK
        }
    }

    public function init(): void
    {
        $this->languages = Core::$cache["languages"];
        unset(Core::$cache["languages"]);
        foreach ($this->getAll() as $language){
            Core::getInstance()->getLogger()->notice("[LANGUAGES] Lang: {$language->getName()} Loaded");
        }
        $this->saveDefaultLanguages();
    }

    /**
     * @return Language[]
     */
    public function getAll(): array
    {
        return $this->languages;
    }

    public static function getDataLanguages(): array
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
        return Managers::DATA()->get("config")->get("prefixes", []);
    }

    public function getDefaultLanguage(): Language
    {
        return reset($this->languages);
    }

    private static function saveDefaultLanguages(): void
    {
        $languages = Managers::DATA()->get("config")->get("languages", []);
        foreach ($languages as $language) {
            Core::getInstance()->saveResource("languages/lang_$language.yml", Core::getInstance()->isInDevMode());
        }
    }
}