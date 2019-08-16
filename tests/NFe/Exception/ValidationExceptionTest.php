<?php
namespace NFe\Exception;

class ValidationExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $errors = ['field' => 'exception details', 'secondField' => 'More details'];
        $validation_exception = new ValidationException($errors);
        $this->assertEquals($errors, $validation_exception->getErrors());
        $this->assertEquals('exception details', $validation_exception->getMessage());
    }
}
