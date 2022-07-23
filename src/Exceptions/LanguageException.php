<?php

namespace Legacy\ThePit\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

final class LanguageException extends Exception
{
    #[Pure] public function __construct(string $message = "", public array $args = [], public int $prefix = 1, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getPrefix(): int
    {
        return $this->prefix;
    }

}