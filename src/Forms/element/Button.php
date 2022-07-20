<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);


namespace Legacy\ThePit\Forms\element;


use Closure;
use Legacy\ThePit\Forms\icon\ButtonIcon;
use Legacy\ThePit\Forms\utils\Submittable;
use JsonSerializable;

final class Button extends \Legacy\ThePit\Forms\element\Element implements JsonSerializable {
    use Submittable;

    private string $text;
    private ?ButtonIcon $icon;

    public function __construct(string $text, ?ButtonIcon $icon = null, ?Closure $listener = null) {
        $this->text = $text;
        $this->icon = $icon;
        $this->setSubmitListener($listener);
    }

    public function hasIcon(): bool {
        return $this->icon !== null;
    }

    public function getIcon(): ?ButtonIcon {
        return $this->icon;
    }

    public function setIcon(?ButtonIcon $icon): void {
        $this->icon = $icon;
    }

    public function jsonSerialize(): array {
        $data = [
            "text" => $this->text
        ];

        if($this->hasIcon()) {
            $data["image"] = $this->icon->jsonSerialize();
        }

        return $data;
    }

}
