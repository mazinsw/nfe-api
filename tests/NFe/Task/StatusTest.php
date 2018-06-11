<?php
namespace NFe\Task;

use NFe\Core\Nota;

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
        $status->fromArray($status->toArray());
        $status->fromArray(null);
    }

    public function testNormalization()
    {
        $status = new Status();
        $status->setAmbiente('1');
        $this->assertEquals(Nota::AMBIENTE_PRODUCAO, $status->getAmbiente());
        $this->assertEquals('1', $status->getAmbiente(true));
        $status->setAmbiente('2');
        $this->assertEquals(Nota::AMBIENTE_HOMOLOGACAO, $status->getAmbiente());
        $this->assertEquals('2', $status->getAmbiente(true));
        $status->setAmbiente('3');
        $this->assertEquals('3', $status->getAmbiente(true));
    }

    public function testStatusLoadInvalidXML()
    {
        $status = self::createStatus();
        $this->setExpectedException('\Exception');
        $status->loadNode(new \DOMDocument());
    }
}
