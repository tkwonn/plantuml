<?php

namespace PlantUML\Repositories;

use PlantUML\Interfaces\ProblemRepositoryInterface;
use PlantUML\Models\Problem;
use PlantUML\Models\TableRow;

class JsonProblemRepository implements ProblemRepositoryInterface
{
    private const RESOURCE_PATH = __DIR__ . '/../Resources/';

    public function findById(int $id): ?Problem
    {
        $path = self::RESOURCE_PATH . $id . '.json';
        if (!file_exists($path)) {
            return null;
        }

        $data = $this->readJsonFile($path);

        return $this->createProblemFromData($data);
    }

    public function findAll(): array
    {
        $tableRows = [];
        $files = glob(self::RESOURCE_PATH . '*.json');

        foreach ($files as $file) {
            $data = $this->readJsonFile($file);
            $tableRows[] = $this->createTableRowFromData($data);
        }

        return $this->sortByID($tableRows);
    }

    private function readJsonFile(string $path): array
    {
        $jsonData = file_get_contents($path);

        return json_decode($jsonData, true);
    }

    private function createProblemFromData(array $data): Problem
    {
        return new Problem(
            $data['id'],
            $data['title'],
            $data['theme'],
            $data['uml']
        );
    }

    private function createTableRowFromData(array $data): TableRow
    {
        return new TableRow(
            $data['id'],
            $data['title'],
            $data['theme']
        );
    }

    private function sortByID(array $tableRows): array
    {
        usort($tableRows, fn (TableRow $a, TableRow $b) => $a->getID() - $b->getID());

        return $tableRows;
    }
}
