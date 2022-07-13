<?php

namespace Legacy\ThePit\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

final class FormsException extends Exception
{
    #[Pure] public function __construct(string $message = "", private array $args = [], private int $prefix = 1, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return int
     */
    public function getPrefix(): int
    {
        return $this->prefix;
    }
}