<?php

namespace Legacy\ThePit\Exceptions;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\utils\CommandException;
use Throwable;

final class LanguageException extends CommandException
{
    #[Pure] public function __construct($message = "", public $args = [], public bool $prefix = true, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getPrefix(): bool {
        return $this->prefix;
    }

}