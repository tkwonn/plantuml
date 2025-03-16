<?php

namespace Helpers;

use Exceptions\HttpException;

class ValidateRequest
{
    /**
     * @throws HttpException
     */
    public static function integer(mixed $value, ?int $min = null, ?int $max = null): int
    {
        $intVal = filter_var($value, FILTER_VALIDATE_INT);
        if ($intVal === false) {
            throw new HttpException(400, 'The provided value is not a valid integer.');
        }

        if ($min !== null && $intVal < $min) {
            throw new HttpException(400, "Value is below minimum ($min).");
        }
        if ($max !== null && $intVal > $max) {
            throw new HttpException(400, "Value is above maximum ($max).");
        }

        return $intVal;
    }
}
