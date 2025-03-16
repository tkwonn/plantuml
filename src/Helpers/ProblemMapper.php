<?php

namespace Helpers;

use Models\Problem;

class ProblemMapper
{
    private const RESOURCE_PATH = __DIR__ . '/../Resources/';

    /**
     * @throws \RuntimeException
     * @return Problem[]
     */
    public function getTableRowData(): array
    {
        $problems = [];
        $files = glob(self::RESOURCE_PATH . '*.json');
        if ($files === false) {
            throw new \RuntimeException('Failed to read JSON files.');
        }

        foreach ($files as $file) {
            $data = $this->readJsonFile($file);
            $problems[] = $this->createProblem($data, false);
        }

        return $this->sortByID($problems);
    }

    public function getProblemById(int $id): ?Problem
    {
        $path = self::RESOURCE_PATH . $id . '.json';
        if (!file_exists($path)) {
            return null;
        }
        $data = $this->readJsonFile($path);

        return $this->createProblem($data, true);
    }

    /**
     * @param  string                                                     $path
     * @throws \RuntimeException
     * @return array{id: int, title: string, theme: string, uml?: string}
     */
    private function readJsonFile(string $path): array
    {
        $jsonData = file_get_contents($path);
        if ($jsonData === false) {
            throw new \RuntimeException('Failed to read JSON file: ' . $path);
        }

        $decoded = json_decode($jsonData, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON file: ' . $path);
        }

        return $decoded;
    }

    /**
     * @param  array{id: int, title: string, theme: string, uml?: string} $data
     * @param  bool                                                       $includeUml
     * @return Problem
     */
    private function createProblem(array $data, bool $includeUml): Problem
    {
        return new Problem(
            $data['id'],
            $data['title'],
            $data['theme'],
            $includeUml ? ($data['uml'] ?? null) : null
        );
    }

    /**
     * @param  Problem[] $problems
     * @return Problem[]
     */
    private function sortByID(array $problems): array
    {
        usort($problems, fn (Problem $a, Problem $b) => $a->getID() - $b->getID());

        return $problems;
    }
}
