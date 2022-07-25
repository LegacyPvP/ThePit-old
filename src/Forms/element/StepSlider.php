<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);


namespace Legacy\ThePit\forms\element;


use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class StepSlider extends Selector
{

    public function getType(): string
    {
        return Element::TYPE_STEP_SLIDER;
    }

    #[Pure] #[ArrayShape(["steps" => "array", "default" => "int"])] public function serializeBody(): array
    {
        return [
            "steps" => $this->getOptionsTexts(),
            "default" => $this->getDefaultIndex()
        ];
    }

}