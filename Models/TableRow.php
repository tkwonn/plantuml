<?php

namespace Models;

class TableRow
{
    public function __construct(
        protected int $id, protected string $title, protected string $theme
    ) {}

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
}