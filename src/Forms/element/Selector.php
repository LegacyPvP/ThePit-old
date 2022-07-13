<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);


namespace Legacy\ThePit\Forms\element;


use InvalidArgumentException;

abstract class Selector extends Element {

    private ?string $submittedOptionId = null;
    private int $defaultIndex = 0;

    /** @var Option[] */
    private $options = [];

    public function getSubmittedOptionId(): ?string {
        return $this->submittedOptionId;
    }

    public function getDefaultIndex(): int {
        return $this->defaultIndex;
    }

    public function setDefaultIndex(int $defaultIndex): void {
        $this->defaultIndex = $defaultIndex;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array {
        return $this->options;
    }

    protected function getOptionsTexts(): array {
        $texts = [];
        foreach($this->options as $option) {
            $texts[] = $option->getText();
        }
        return $texts;
    }

    public function getOption(string $id): ?Option {
        foreach($this->options as $option) {
            if($option->getId() === $id) {
                return $option;
            }
        }
        return null;
    }

    public function getOptionByIndex(int $index): ?Option {
        return $this->options[$index] ?? null;
    }

    public function addOption(Option $option): void {
        if($this->getOption($option->getId()) !== null) {
            throw new InvalidArgumentException("There's an option with that name already!");
        }
        $this->options[] = $option;
    }

    public function removeOption(string $id): void {
        foreach($this->options as $key => $option) {
            if($option->getId() === $id) {
                unset($this->options[$key]);
            }
        }
    }

    public function assignResult($result): void {
        $this->submittedOptionId = $this->getOptionByIndex($result)->getId();
    }

}