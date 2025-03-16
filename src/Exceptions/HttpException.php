<?php

namespace Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * @var array<int, string>
     */
    private static array $statusPhrases = [
        400 => 'Bad Request',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
    ];

    public function __construct(int $statusCode, string $message)
    {
        $statusPhrase = self::$statusPhrases[$statusCode];
        $errorMessage = $statusPhrase . ': ' . $message;
        parent::__construct($errorMessage, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }

    public function getErrorMessage(): string
    {
        return $this->getMessage();
    }
}
