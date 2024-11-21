<?php

use Database\ProblemDaoImpl;
use Exceptions\HttpException;
use Helpers\UMLConvertor;
use Helpers\ValidateAPIRequest;
use Helpers\ValidateArgHelper;
use Response\HTTPRenderer;
use Response\Render\FileRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\SVGRenderer;

$problemDao = new ProblemDaoImpl(__DIR__ . '/../resources/');

return [
    '' => function () use ($problemDao): HTTPRenderer {
        $tableRows = $problemDao->getAllProblems();
        $perPage = 5;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        return new HTMLRenderer('home', [
            'items' => array_slice($tableRows, $offset, $perPage),
            'page' => $page,
            'totalPages' => ceil(count($tableRows) / $perPage),
        ]);
    },
    'problems' => function () use ($problemDao): HTTPRenderer {
        try {
            $id = ValidateArgHelper::integer($_GET['id'] ?? null, 1);
        } catch (\InvalidArgumentException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $problem = $problemDao->getProblemById((int) $id);

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
