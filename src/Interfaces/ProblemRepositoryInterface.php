<?php

namespace Interfaces;

use Models\Problem;

interface ProblemRepositoryInterface
{
    public function findById(int $id): ?Problem;

    public function findAll(): array;
}
