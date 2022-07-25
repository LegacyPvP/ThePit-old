<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);


namespace Legacy\ThePit\forms\element;


use JetBrains\PhpStorm\Pure;

final class Label extends Element
{

    #[Pure] public function __construct(string $headerText)
    {
        parent::__construct($headerText);
    }

    public function getType(): string
    {
        return Element::TYPE_LABEL;
    }

    public function assignResult($result): void
    {
        return;
    }

    public function serializeBody(): array
    {
        return [];
    }

}