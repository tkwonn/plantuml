<?php

namespace Helpers;

use Models\Problem;

class ProblemMapper
{
    private const RESOURCE_PATH = __DIR__ . '/../resources/';

    public function getProblemById(int $id): ?Problem
    {
        $path = self::RESOURCE_PATH . $id . '.json';
        if (!file_exists($path)) {
            return null;
        }
        $data = $this->readJsonFile($path);

        return $this->createProblem($data, true);
    }

    public function getTableRowData(): array
    {
        $problems = [];
        $files = glob(self::RESOURCE_PATH . '*.json');
        foreach ($files as $file) {
            $data = $this->readJsonFile($file);
            $problems[] = $this->createProblem($data, false);
        }

        return $this->sortByID($problems);
    }

    private function readJsonFile(string $path): array
    {
        $jsonData = file_get_contents($path);

        return json_decode($jsonData, true);
    }

    private function createProblem(array $data, bool $includeUml): Problem
    {
        return new Problem(
            $data['id'],
            $data['title'],
            $data['theme'],
            $includeUml ? $data['uml'] : null
        );
    }

    private function sortByID(array $problems): array
    {
        usort($problems, fn (Problem $a, Problem $b) => $a->getID() - $b->getID());

        return $problems;
    }
}
