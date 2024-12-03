<?php

namespace Models;

class Problem extends TableRow
{
    public function __construct(
        int $id,
        string $title,
        string $theme,
        private string $uml
    ) {
        parent::__construct($id, $title, $theme);
    }

    public function getUML(): string
    {
        return $this->uml;
    }
}
