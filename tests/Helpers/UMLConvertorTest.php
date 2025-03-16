<?php

namespace Tests\Helpers;

use Helpers\UMLConvertor;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UMLConvertorTest extends TestCase
{
    public function testConvertUMLWithValidInput()
    {
        $uml_code = <<<'EOD'
@startuml
Bob -> Alice : hello
@enduml
EOD;
        $format = 'svg';

        $result = UMLConvertor::convertUML($uml_code, $format);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('<svg', $result);
    }

    public function testConvertUMLWithInvalidInput()
    {
        $uml_code = 'invalid uml code';
        $format = 'svg';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot be converted to UML diagram. Please check your code.');

        UMLConvertor::convertUML($uml_code, $format);
    }
}
