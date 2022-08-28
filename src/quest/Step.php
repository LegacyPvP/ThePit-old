<?php

namespace Legacy\ThePit\quest;

use Legacy\ThePit\traits\CallBackTrait;
use Legacy\ThePit\utils\RewardUtils;

final class Step
{
    use CallBackTrait;

    private bool $completed = false;

    /**
     * Yanoox : Je ne vois pas pourquoi je devrais crée plusieurs type (casser block, tuer une entité...) alors que le callable fait tout
     * Yanoox: suffira juste d'implémenter dans les listeners l'execution du callback
     */
    public function __construct(protected int $id, protected string $listenerType, callable $callable, protected ?RewardUtils $rewardUtils)
    {
        $this->addCallback($this->listenerType, $callable);
    }

    public static function create(int $id, string $listenerType, callable $callable, ?RewardUtils $rewardUtils): self
    {
        return new self($id, $listenerType, $callable, $rewardUtils);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReward(): ?RewardUtils
    {
        return $this->rewardUtils;
    }

    public function init(): void
    {
        $this->executeCallBack($this->listenerType, $this);
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(): void
    {
        $this->completed = true;
    }
}