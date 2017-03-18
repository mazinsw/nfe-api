<?php
namespace NFe\Task;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public static function createStatus()
    {
        $status = new Status();
        return $status;
    }

    public function testStatusAssign()
    {
        $status = self::createStatus();
        $status->fromArray($status);
        $status->fromArray(null);
    }

    public function testStatusLoadInvalidXML()
    {
        $status = self::createStatus();
        $this->setExpectedException('\Exception');
        $status->loadNode(new \DOMDocument());
    }
}
