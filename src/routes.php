<?php

use Exceptions\HttpException;
use Helpers\ProblemMapper;
use Helpers\UMLConvertor;
use Helpers\ValidateRequest;
use Response\HTTPRenderer;
use Response\Render\FileRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\SVGRenderer;

$problemMapper = new ProblemMapper();

return [
    '' => function () use ($problemMapper): HTTPRenderer {
        $tableRows = $problemMapper->getTableRowData();
        $perPage = 5;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        return new HTMLRenderer('home', [
            'items' => array_slice($tableRows, $offset, $perPage),
            'page' => $page,
            'totalPages' => ceil(count($tableRows) / $perPage),
        ]);
    },
    'problems' => function () use ($problemMapper): HTTPRenderer {
        try {
            $id = ValidateRequest::integer($_GET['id'] ?? null, 1);
        } catch (\InvalidArgumentException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $problem = $problemMapper->getProblemById($id);
        if ($problem === null) {
            throw new HttpException(404, 'Problem #' . $id . ' not found');
        }

        return new HTMLRenderer('problem', ['problem' => $problem]);
    },
    'api/uml/preview' => function (): HTTPRenderer {
        try {
            $content = UMLConvertor::convertUML($_POST['uml'], $_POST['format']);
        } catch (\RuntimeException $e) {
            throw new Exception($e->getMessage());
        }

        return new SVGRenderer($content);
    },
    'api/uml/export' => function (): HTTPRenderer {
        try {
            $id = ValidateRequest::integer($_GET['id'] ?? null, 1);
        } catch (\InvalidArgumentException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $uml = $_POST['uml'] ?? null;
        $format = $_POST['format'] ?? null;

        if ($uml === null || $format === null) {
            throw new HttpException(400, 'Missing uml or format parameter');
        }

        $contentTypes = [
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain; charset=utf-8',
        ];

        if ($format === 'txt') {
            $content = $uml;
        } else {
            try {
                $content = UMLConvertor::convertUML($uml, $format);
            } catch (\RuntimeException $e) {
                throw new Exception($e->getMessage());
            }
        }
        $filename = sprintf('problem%d-diagram.%s', $id, $format);

        return new FileRenderer(
            $content,
            $contentTypes[$format],
            $filename
        );
    },
];
