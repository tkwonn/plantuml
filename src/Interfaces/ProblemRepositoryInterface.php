<?php

namespace PlantUML\Interfaces;

use PlantUML\Models\Problem;

interface ProblemRepositoryInterface
{
    public function findById(int $id): ?Problem;

    public function findAll(): array;
}
