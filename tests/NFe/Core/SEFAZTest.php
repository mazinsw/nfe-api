<?php
namespace NFe\Core;

class SEFAZTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testInstancia()
    {
        $sefaz = \NFe\Core\SEFAZ::getInstance();
        $this->assertNotNull($sefaz);
        $this->assertNotNull($sefaz->getConfiguracao());
    }

    public function testNotas()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $sefaz->addNota(new \NFe\Core\NFCe());
        $sefaz->addNota(new \NFe\Core\NFCe());
        $sefaz->fromArray($sefaz);
        $sefaz->fromArray($sefaz->toArray());
        $sefaz->fromArray(null);
        $this->assertCount(2, $sefaz->getNotas());
    }

    public function testAutoriza()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $sefaz->setNotas(array());
        $this->assertEquals(0, $sefaz->autoriza());
    }

    public function testConsulta()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $this->assertEquals(0, $sefaz->consulta(array()));
    }

    public function testExecuta()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $this->assertEquals(0, $sefaz->executa(array()));
    }
}
