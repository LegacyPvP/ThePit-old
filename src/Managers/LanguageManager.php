<?php
namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Databases\LanguageDatabase;
use Legacy\ThePit\Objects\Language;
use Legacy\ThePit\Utils\ServerUtils;

abstract class LanguageManager
{
    /**
     * @var Language[]
     */
    public static array $languages = [];

    public static function initLanguages(): void {
        @mkdir(Core::getInstance()->getDataFolder()."languages");
        self::saveDefaultConfig();
        foreach (self::getConfigLanguages() as $language){
            self::$languages[$language] = new Language($language, new LanguageDatabase($language));
            Core::getInstance()->getLogger()->notice("[LANGUAGES] Lang: $language Loaded");
        }
    }

    /**
     * @return Language[]
     */
    public static function getLanguages(): array
    {
        return self::$languages;
    }

    public static function getConfigLanguages(): array
    {
        $languages = [];
        foreach (scandir(Core::getInstance()->getDataFolder() . "languages") as $file) {
            if($file === "." or $file === "..") continue;
            $languages[] = str_replace([".yml", "lang_"], ["", ""], $file);
        }
        return $languages;
    }

    public static function parseLanguage(string $language): Language|bool
    {
        return self::$languages[$language] ?? reset(self::$languages);
    }

    public static function getPrefix(int $prefix): string {
        $result = match($prefix) {
            1 => ServerUtils::PREFIX_1_TEXT,
            2 => ServerUtils::PREFIX_2_TEXT,
            3 => ServerUtils::PREFIX_3_TEXT,
        };

        return $result ?? ServerUtils::PREFIX_1;
    }

    public static function getDefaultLanguage(): Language
    {
        return reset(self::$languages);
    }

    private static function saveDefaultConfig(): void
    {
        $languages = Core::getInstance()->getConfig()->get("languages", []);
        foreach ($languages as $language){
            Core::getInstance()->saveResource("languages/lang_$language.yml", true);
        }
    }
}