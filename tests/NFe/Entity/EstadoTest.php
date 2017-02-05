<?php
namespace NFe\Entity;

class EstadoTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance();
    }

    public function testEstado()
    {
        $estado = new \NFe\Entity\Estado();
        $estado->setNome('Paraná');
        $estado->setUF('PR');
        $estado->checkCodigos();
        $estado->fromArray($estado);
        $estado->fromArray($estado->toArray());
        $estado->fromArray(null);

        $this->assertEquals(41, $estado->getCodigo());
        $this->assertEquals('Paraná', $estado->getNome());
        $this->assertEquals('Paraná', $estado->getNome(true));
        $this->assertEquals('PR', $estado->getUF());
    }
}
