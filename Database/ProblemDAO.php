<?php

namespace Database;

use Models\Problem;

interface ProblemDAO
{
    public function getProblemById(int $id): ?Problem;

    public function getAllProblems(): array;
}
