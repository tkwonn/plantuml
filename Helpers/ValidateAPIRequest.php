<?php
namespace Helpers;

use Exceptions\HttpException;
class ValidateAPIRequest
{

    /**
     * @param string|null $format The requested format.
     * @return array An array containing 'uml_code' and 'format'.
     * @throws HttpException If the request method is not POST or parameters are invalid.
     */
    public static function uml(?string $format): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new HttpException(405, 'Request method must be POST');
        }

        $uml_code = $_POST['uml'] ?? '';
        if (empty($uml_code)) {
            throw new HttpException(400, 'UML code is required');
        }

        if (!in_array($format, ['svg', 'png'])) {
            throw new HttpException(400, 'Valid format is required');
        }

        return [
            'uml_code' => $uml_code,
            'format' => $format
        ];
    }
}