<?php
namespace NFe\Log;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    public function testLogs()
    {
        \NFe\Log\Logger::getInstance()->fromArray(\NFe\Log\Logger::getInstance());
        \NFe\Log\Logger::getInstance()->fromArray(\NFe\Log\Logger::getInstance()->toArray());
        \NFe\Log\Logger::getInstance()->fromArray(null);
        \NFe\Log\Logger::error('Error Test');
        \NFe\Log\Logger::warning('Warning Test');
        \NFe\Log\Logger::debug('Debug Test');
        \NFe\Log\Logger::information('Information Test');
        \NFe\Log\Logger::getInstance()->error('Error Test');
        \NFe\Log\Logger::getInstance()->warning('Warning Test');
        \NFe\Log\Logger::getInstance()->debug('Debug Test');
        \NFe\Log\Logger::getInstance()->information('Information Test');
    }

    public function testUndefinedStaticMethod()
    {
        $this->setExpectedException('\BadMethodCallException');
        \NFe\Log\Logger::erro('Error Test');
        $this->setExpectedException(null);
    }

    public function testUndefinedMethod()
    {
        $this->setExpectedException('\BadMethodCallException');
        \NFe\Log\Logger::getInstance()->erro('Error Test');
        $this->setExpectedException(null);
    }

    public function testCannotWriteFile()
    {
        $log = new \NFe\Log\Logger();
        $log->setDirectory('C:/..');
        $this->setExpectedException('\Exception');
        $log->error('Cannot write file');
        $this->setExpectedException(null);
    }
}
