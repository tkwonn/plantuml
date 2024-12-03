<?php

use PlantUML\Exceptions\HttpException;
use PlantUML\Helpers\UMLConvertor;
use PlantUML\Helpers\ValidateAPIRequest;
use PlantUML\Helpers\ValidateArgHelper;
use PlantUML\Repositories\JsonProblemRepository;
use PlantUML\Response\HTTPRenderer;
use PlantUML\Response\Render\FileRenderer;
use PlantUML\Response\Render\HTMLRenderer;
use PlantUML\Response\Render\SVGRenderer;

$problemRepository = new JsonProblemRepository();

return [
    '' => function () use ($problemRepository): HTTPRenderer {
        $tableRows = $problemRepository->findAll();
        $perPage = 5;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        return new HTMLRenderer('home', [
            'items' => array_slice($tableRows, $offset, $perPage),
            'page' => $page,
            'totalPages' => ceil(count($tableRows) / $perPage),
        ]);
    },
    'problems' => function () use ($problemRepository): HTTPRenderer {
        try {
            $id = ValidateArgHelper::integer($_GET['id'] ?? null, 1);
        } catch (\InvalidArgumentException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $problem = $problemRepository->findById($id);

        if ($problem === null) {
            throw new HttpException(404, 'Problem #' . $id . ' not found');
        }

        return new HTMLRenderer('problem', ['problem' => $problem]);
    },
    'api/uml/preview' => function (): HTTPRenderer {
        $data = ValidateAPIRequest::uml($_POST['format'] ?? null);

        try {
            $svgContent = UMLConvertor::convertUML($data['uml_code'], $data['format']);
        } catch (\RuntimeException $e) {
            throw new Exception($e->getMessage());
        }

        return new SVGRenderer($svgContent);
    },
    'api/uml/export' => function (): HTTPRenderer {
        $data = ValidateAPIRequest::uml($_POST['format'] ?? null);

        try {
            $content = UMLConvertor::convertUML($data['uml_code'], $data['format']);
        } catch (\RuntimeException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $contentTypes = [
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain; charset=utf-8',
        ];

        return new FileRenderer(
            $content,
            $contentTypes[$data['format']],
            "uml-diagram.{$data['format']}"
        );
    },
];
