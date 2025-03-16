<?php

use Exceptions\HttpException;
use Helpers\ProblemMapper;
use Helpers\UMLConvertor;
use Helpers\ValidateRequest;
use Response\HTTPRenderer;
use Response\Render\FileRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\SVGRenderer;

return [
    // HTML pages
    '' => function (): HTTPRenderer {
        $problemMapper = new ProblemMapper();
        $tableRows = $problemMapper->getTableRowData();
        $perPage = 5;
        $page = ValidateRequest::integer($_GET['page'] ?? 1, 1);
        $offset = ($page - 1) * $perPage;

        return new HTMLRenderer('home', [
            'items' => array_slice($tableRows, $offset, $perPage),
            'page' => $page,
            'totalPages' => ceil(count($tableRows) / $perPage),
        ]);
    },
    'problems' => function (): HTTPRenderer {
        $id = ValidateRequest::integer($_GET['id'] ?? null, 1);

        $problemMapper = new ProblemMapper();
        $problem = $problemMapper->getProblemById($id);
        if ($problem === null) {
            throw new HttpException(404, 'Problem #' . $id . ' not found');
        }

        return new HTMLRenderer('problem', ['problem' => $problem]);
    },
    // API endpoints
    'api/uml/preview' => function (): HTTPRenderer {
        $uml = $_POST['uml'] ?? null;
        $format = $_POST['format'] ?? null;

        if (!is_string($uml) || !is_string($format)) {
            throw new HttpException(400, 'Missing or invalid "uml" or "format" parameter');
        }
        $content = UMLConvertor::convertUML($uml, $format);

        return new SVGRenderer($content);
    },
    'api/uml/export' => function (): HTTPRenderer {
        $id = ValidateRequest::integer($_GET['id'] ?? null, 1);
        $uml = $_POST['uml'] ?? null;
        $format = $_POST['format'] ?? null;

        if (!is_string($uml) || !is_string($format)) {
            throw new HttpException(400, 'Missing or invalid "uml" or "format" parameter');
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
