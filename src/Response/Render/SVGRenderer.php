<?php

namespace PlantUML\Response\Render;

use PlantUML\Response\HTTPRenderer;

class SVGRenderer implements HTTPRenderer
{
    public function __construct(
        private string $svgContent
    ) {
    }

    public function getFields(): array
    {
        return [
            'Content-Type' => 'image/svg+xml',
        ];
    }

    public function getContent(): string
    {
        return $this->svgContent;
    }
}