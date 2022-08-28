<?php

namespace Legacy\ThePit\quest;

use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\traits\CacheTrait;
use Legacy\ThePit\traits\CallBackTrait;
use Legacy\ThePit\utils\RewardUtils;

final class Quest
{
    const CURRENT_STEP = "current_step";
    private bool $completed = false;
    use CacheTrait;
    use CallBackTrait;

    /**
     * @param string $name
     * @param Step[] $steps
     * @param RewardUtils $rewardUtils
     */
    public function __construct(protected string $name, protected array $steps, protected RewardUtils $rewardUtils)
    {
        self::$cache = [
            self::CURRENT_STEP => 0,
        ];
    }

    public static function create(string $name, array $steps, RewardUtils $rewardUtils): self
    {
        return new self($name, $steps, $rewardUtils);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Step[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getStep(int $step): Step
    {
        return $this->steps[$step];
    }

    public function getCurrentStep(): Step
    {
        return $this->getStep(self::$cache[self::CURRENT_STEP]);
    }

    public function nextStep(): void
    {
        if ((self::$cache[self::CURRENT_STEP] + 1) < count($this->getSteps())) {
            self::$cache[self::CURRENT_STEP] = self::$cache[self::CURRENT_STEP] + 1;
            return;
        }
        $this->completed = true;
    }

    public function init(LegacyPlayer $player): void
    {
        if ($this->completed) {
            $this->rewardUtils->request($player);
            return;
        }
        $this->nextStep();
    }

    public function getFinalReward(): RewardUtils
    {
        return $this->rewardUtils;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }
}