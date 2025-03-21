<?php

namespace Helpers;

use RuntimeException;

class UMLConvertor
{
    private const JAR_PATH = __DIR__ . '/../lib/plantuml-1.2024.7.jar';
    private const TMP_PATH = __DIR__ . '/../../tmp';

    public static function convertUML(string $uml_code, string $format): string
    {
        $uid = uniqid('uml_', true);
        $input_path = self::TMP_PATH . "/$uid.txt";
        $output_path = self::TMP_PATH . "/$uid.$format";

        try {
            file_put_contents($input_path, $uml_code);

            $command = sprintf(
                'java -Dfile.encoding=UTF-8 -jar %s -t%s %s -o %s',
                escapeshellarg(self::JAR_PATH),
                escapeshellarg($format),
                escapeshellarg($input_path),
                escapeshellarg(dirname($output_path))
            );
            shell_exec($command);

            if (!file_exists($output_path)) {
                throw new RuntimeException('Cannot be converted to UML diagram. Please check your code.');
            }

            $contents = file_get_contents($output_path);
            if ($contents === false) {
                throw new RuntimeException('Failed to read the output file.');
            }

            return $contents;
        } finally {
            @unlink($input_path);
            @unlink($output_path);
        }
    }
}
