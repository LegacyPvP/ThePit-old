<?php /** @noinspection PhpParamsInspection */

namespace Legacy\ThePit\providers;

use Legacy\ThePit\perks\Perk;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\event\Event;
use pocketmine\Server;

final class PerksProvider
{
    /**
     * @var Perk[]
     */
    private array $perks = [];

    public function __construct(private LegacyPlayer $player)
    {
        $this->add(Perk::BOUNTYHUNTER(), Perk::NABBIT(), Perk::GOLDENHEAD(), Perk::FLASH(), Perk::SERIALKILLER());
    }

    public function getAll(): array
    {
        return $this->perks;
    }

    public function add(Perk ...$perks): void
    {
        foreach ($perks as $perk){
            $this->perks[$perk->getName()] = $perk;
        }
    }

    public function remove(Perk ...$perks): void
    {
        foreach ($perks as $perk){
            unset($this->perks[$perk->getName()]);
        }
    }

    public function onEvent(Event $event)
    {
        foreach ($this->getAll() as $perk) {
            if ($perk->onEvent() === $event::class and $perk->canStart($event)) {
                $perk->start($event);
            }
        }
    }
}