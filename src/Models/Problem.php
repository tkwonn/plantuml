<?php

namespace PlantUML\Models;

class Problem
{
    public function __construct(
        private int $id,
        private string $title,
        private string $theme,
        private ?string $uml = null
    ) {
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getUML(): ?string
    {
        return $this->uml;
    }
}
