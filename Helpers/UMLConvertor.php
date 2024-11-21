<?php

namespace Helpers;

use RuntimeException;

class UMLConvertor
{
    private static string $jar_path = __DIR__ . '/../lib/plantuml-1.2024.7.jar';
    private static string $tmp_path = __DIR__ . '/../tmp';

    public static function convertUML(string $uml_code, string $format = 'svg'): string
    {
        $uid = uniqid('uml_', true);
        $input_path = self::$tmp_path . "/$uid.txt";
        $output_path = self::$tmp_path . "/$uid.$format";

        try {
            file_put_contents($input_path, $uml_code);

            $command = sprintf(
                'java -Dfile.encoding=UTF-8 -jar %s -t%s %s -o %s',
                escapeshellarg(self::$jar_path),
                escapeshellarg($format),
                escapeshellarg($input_path),
                escapeshellarg(dirname($output_path))
            );
            shell_exec($command);

            if (!file_exists($output_path)) {
                throw new RuntimeException('Cannot be converted to UML diagram. Please check your code.');
            }

            return file_get_contents($output_path);
        } finally {
            @unlink($input_path);
            @unlink($output_path);
        }
    }
}
