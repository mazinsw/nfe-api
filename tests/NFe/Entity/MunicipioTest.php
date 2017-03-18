<?php
namespace NFe\Entity;

class MunicipioTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
    }

    public function testMunicipio()
    {
        $municipio = new \NFe\Entity\Municipio();
        $municipio->setNome('Paranavaí');
        $estado = new \NFe\Entity\Estado();
        $estado->setNome('Paraná');
        $estado->setUF('PR');
        $municipio->setEstado($estado);
        $municipio->checkCodigos();
        $municipio->fromArray($municipio);
        $municipio->fromArray($municipio->toArray());
        $municipio->fromArray(null);

        $this->assertEquals(4118402, $municipio->getCodigo());
        $this->assertEquals('Paranavaí', $municipio->getNome());
    }
}
