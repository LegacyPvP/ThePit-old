<?php

namespace Legacy\ThePit\Managers;

use pocketmine\utils\EnumTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static CommandsManager COMMANDS()
 * @method static CooldownManager COOLDOWNS()
 * @method static CurrenciesManager CURRENCIES()
 * @method static CustomItemsManager CUSTOMITEMS()
 * @method static EntitiesManager ENTITIES()
 * @method static EventsManager EVENTS()
 * @method static FormsManager FORMS()
 * @method static ItemsManager ITEMS()
 * @method static KnockBackManager KNOCKBACK()
 * @method static LanguageManager LANGUAGES()
 * @method static ListenersManager LISTENERS()
 * @method static MutesManager MUTES()
 * @method static PrestigesManager PRESTIGES()
 * @method static RanksManager RANKS()
 * @method static ScoreBoardsManager SCOREBOARDS()
 */
abstract class Managers implements Manager
{
    use EnumTrait {
        __construct as Enum___construct;
    }

    public static function loadManagers(): void
    {
        foreach (self::getManagers() as $manager) {
            $manager->load();
        }
    }

    public static function initManagers(): void
    {
        self::setup();
    }

    /**
     * @return Managers[]
     */
    protected static function getManagers(): array
    {
        return [
            new CommandsManager('commands'),
            new CooldownManager('cooldowns'),
            new CurrenciesManager('currencies'),
            new CustomItemsManager('customitems'),
            new EntitiesManager('entities'),
            new EventsManager('events'),
            new FormsManager('forms'),
            new ItemsManager('items'),
            new KnockBackManager('knockback'),
            new LanguageManager('languages'),
            new ListenersManager('listeners'),
            new MutesManager('mutes'),
            new PrestigesManager('prestiges'),
            new RanksManager('ranks'),
            new ScoreBoardsManager('scoreboards')
        ];
    }

    protected static function setup(): void
    {
        foreach (self::getManagers() as $manager) {
            self::register($manager);
            $manager->init();
        }
    }

    public function getAll(): array
    {
        return [];
    }

    public function init(): void
    {

    }

    public function get(string $name): ?object
    {
        return null;
    }

    public function load(): void
    {

    }

}