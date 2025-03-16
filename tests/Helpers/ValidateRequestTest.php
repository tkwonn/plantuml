<?php

namespace Tests\Helpers;

use Exceptions\HttpException;
use Helpers\ValidateRequest;
use PHPUnit\Framework\TestCase;

class ValidateRequestTest extends TestCase
{
    public function testIntegerWithValidInput()
    {
        $value = '42';

        $result = ValidateRequest::integer($value);

        $this->assertEquals(42, $result);
    }

    public function testIntegerWithInvalidInput()
    {
        $value = 'not-an-integer';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('The provided value is not a valid integer.');

        ValidateRequest::integer($value);
    }

    public function testIntegerBelowMinimum()
    {
        $value = '5';
        $min = 10;

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Value is below minimum ($min).");

        ValidateRequest::integer($value, $min);
    }

    public function testIntegerAboveMaximum()
    {
        $value = '20';
        $max = 15;

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Value is above maximum ($max).");

        ValidateRequest::integer($value, null, $max);
    }
}
