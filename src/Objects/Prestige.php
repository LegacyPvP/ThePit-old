<?php

namespace Legacy\ThePit\Objects;

final class Prestige {

    private array $levels;
    private string $name;
    private string $nametag;

    public function __construct(array $levels, string $name, string $nametag)
    {
        $this->levels = $levels;
        $this->name = $name;
        $this->nametag = $nametag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    //NAMETAG
    const PRESTIGE_0 = "§7[§e§l0§r§7 - §r{level}§r§7]";
    const PRESTIGE_1 = "§7[§e§lI§r§7 - §r{level}§r§7]";
    const PRESTIGE_2 = "§b[§e§lIII§r§b - §r{level}§r§b]";
    const PRESTIGE_3 = "§b§l[§e§lIII§r§b - §r{level}§r§b§l]§r";
    const PRESTIGE_4 = "§a[§e§lIV§r§a - §r{level}§r§b]";
    const PRESTIGE_5 = "§a§l[§e§lV§r§a - §r{level}§r§a§l]§r";
    const PRESTIGE_6 = "§e[§e§lIV§r§e - §r{level}§r§e]";
    const PRESTIGE_7 = "§e§l[§e§lV§r§e - §r{level}§r§e§l]§r";

    //NAMES
    const PRESTIGE_LEVEL_1 = "0";

    //LEVELS
    const PRESTIGE_LEVELS_REACH_1 = [
        1 => 4,
        2 => 9,
        3 => 13,
        4 => 18,
        5 => 22,
        6 => 26,
        7 => 31,
        8 => 35,
        9 => 40,
        10 => 44,
        11 => 48,
        12 => 53,
        13 => 57,
        14 => 62,
        15 => 66,
        16 => 70,
        17 => 75,
        18 => 79,
        19 => 84,
        20 => 88,
        21 => 92,
        22 => 97,
        23 => 101,
        24 => 106,
        25 => 110,
        26 => 114,
        27 => 119,
        28 => 123,
        29 => 128,
        30 => 132,
        31 => 136,
        32 => 141,
        33 => 145,
        34 => 150,
        35 => 154,
        36 => 158,
        37 => 163,
        38 => 167,
        39 => 172,
        40 => 176,
        41 => 180,
        42 => 185,
        43 => 189,
        44 => 194,
        45 => 198,
        46 => 202,
        47 => 207,
        48 => 211,
        49 => 216,
        50 => 220,
        51 => 224,
        52 => 229,
        53 => 233,
        54 => 238,
        55 => 242,
        56 => 246,
        57 => 251,
        58 => 255,
        59 => 259,
        60 => 264,
        61 => 268,
        62 => 273,
        63 => 277,
        64 => 281,
        65 => 286,
        66 => 290,
        67 => 295,
        68 => 299,
        69 => 303,
        70 => 308,
        71 => 312,
        72 => 317,
        73 => 321,
        74 => 325,
        75 => 330,
        76 => 334,
        77 => 339,
        78 => 343,
        79 => 347,
        80 => 352,
        81 => 356,
        82 => 361,
        83 => 365,
        84 => 369,
        85 => 374,
        86 => 378,
        87 => 383,
        88 => 387,
        89 => 391,
        90 => 396,
        91 => 400,
        92 => 404,
        93 => 409,
        94 => 413,
        95 => 417,
        96 => 422,
        97 => 426,
        98 => 430,
        99 => 435,
        100 => 440,
    ];
}