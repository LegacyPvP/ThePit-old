<?php

namespace Legacy\ThePit\crate;

use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;

final class Reward
{

    public function __construct(protected array $xp, protected array $items, protected array $money)
    {
    }

    public static function create(array $xp, array $items, array $money): self
    {
        return new self($xp, $items, $money);
    }

    public function request(LegacyPlayer $player): void
    {
        $money = $this->getMoney();
        $xp = $this->getXp();
        $item = $this->getItem();
        if (!is_null($money)) {
            foreach ($money as $m) {
                $player->getCurrencyProvider()->add($m["type"], $m["money"]);
            }
        }
        !is_null($xp) ?: $player->getXpManager()->addXp($xp);
        !is_null($item) ?: $player->getInventory()->addItem($item);
        $player->sendMessage("You received $xp xps !");
        foreach ($money as $m) {
            $player->sendMessage("You receive " . $m[1] . Managers::CURRENCIES()->get($m[0])->getName());
        }
    }

    public function getMoney(): ?array
    {
        $moneys = [];
        if (empty($this->money)) return null;
        foreach ($this->money as $type => $data) {
            while (true) {
                if (mt_rand(0, 99) <= (int)$data["chance"]) {
                    foreach ($data["amount"] as $chance => $money) {
                        if (mt_rand(0, 99) <= (int)$chance) {
                            $moneys[] = [
                                "type" => $type,
                                "money" => $money
                            ];
                            break;
                        }
                    }
                    break;
                }
            }
        }
        return $moneys;
    }

    public function getXp(): ?int
    {
        if ($this->xp == 0) return null;
        while (true) {
            foreach ($this->xp as $chance => $xp) {
                if (mt_rand(0, 99) <= $chance) return $xp;
            }
        }
    }

    public function getItem(): ?Item
    {
        if (empty($this->items)) return null;
        while (true) {
            foreach ($this->items as $item => $data) {
                if (mt_rand(0, 99) <= $data["chance"]) {
                    foreach ($data["amount"] as $chance => $amount) {
                        if (mt_rand(0, 99) <= $chance) {
                            return StringToItemParser::getInstance()->parse($item)->setCount($amount);
                        }
                    }
                }
            }
        }
    }
}