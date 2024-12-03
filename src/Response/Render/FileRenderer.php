<?php

namespace PlantUML\Response\Render;

use PlantUML\Response\HTTPRenderer;

class FileRenderer implements HTTPRenderer
{
    public function __construct(
        private string $content,
        private string $contentType,
        private string $fileName
    ) {
    }

    public function getFields(): array
    {
        return [
            'Content-Type' => $this->contentType,
            'Content-Disposition' => 'attachment; filename="' . $this->fileName . '"',
        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
