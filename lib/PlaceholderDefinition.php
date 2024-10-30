<?php

namespace Novatorius\Blueprint;

class PlaceholderDefinition {
    public function __construct(
        public readonly string $start,
        public readonly string $end,
        protected array $input
    ) {
    }

    public function getInput(): array
    {
        return $this->input;
    }
}