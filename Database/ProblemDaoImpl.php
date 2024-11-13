<?php
namespace Database;

use Models\Problem;
use Models\TableRow;

class ProblemDaoImpl implements ProblemDAO
{
    private string $problemResourcesPath;

    public function __construct(string $problemResourcesPath)
    {
        $this->problemResourcesPath = $problemResourcesPath;
    }

    public function getProblemById(int $id): ?Problem
    {
        $path = $this->problemResourcesPath . $id . '.json';
        if (!file_exists($path)) {
            return null;
        }

        $jsonData = file_get_contents($path);
        $data = json_decode($jsonData, true);

        return new Problem(
            $data['id'],
            $data['title'],
            $data['theme'],
            $data['uml']
        );
    }

    public function getAllProblems(): array
    {
        $tableRows = [];
        $files = glob($this->problemResourcesPath . '*.json');
        foreach($files as $file) {
            $jsonData = file_get_contents($file);
            $data = json_decode($jsonData, true);

            $tableRows[] = new TableRow(
                $data['id'],
                $data['title'],
                $data['theme']
            );
        }

        // Sort the table rows by ID
        usort($tableRows, function (TableRow $a, TableRow $b) {
            return $a->getID() - $b->getID();
        });

        return $tableRows;
    }
}