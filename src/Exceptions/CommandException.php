<?php

namespace Legacy\ThePit\exceptions;

use JetBrains\PhpStorm\Pure;
use Throwable;

final class CommandException extends \pocketmine\command\utils\CommandException
{
    #[Pure] public function __construct($message = "", public $args = [], public bool $prefix = true, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getPrefix(): bool
    {
        return $this->prefix;
    }

}