<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Objects\Grade;

abstract class GradesManager
{
    /**
     * @var array
     */
    private static array $grades = [];

    public static function initGrades(): void
    {
        foreach (Core::getInstance()->getConfig()->get('grades', []) as $name => $grade) {
            self::$grades[$name] = new Grade($name, $grade["permissions"], $grade["chat"], $grade["nametag"], $grade["scoretag"]);
            Core::getInstance()->getLogger()->notice("[GRADES] Grade: $name Loaded");
        }
    }

    /**
     * @return array
     */
    public static function getGrades(): array
    {
        return self::$grades;
    }

    public static function parseGrade(string $grade): ?Grade
    {
        return self::$grades[$grade];
    }

    public static function getDefaultGrade(): ?Grade
    {
        return reset(self::$grades) ?? null;
    }

}