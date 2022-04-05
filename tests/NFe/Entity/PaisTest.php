<?php

namespace NFe\Entity;

class PaisTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
    }

    public function testPais()
    {
        $pais = new \NFe\Entity\Pais();
        $pais->setCodigo(1058);
        $pais->setNome('Brasil');
        $pais->fromArray($pais);
        $pais->fromArray($pais->toArray());
        $pais->fromArray(null);

        $this->assertEquals(1058, $pais->getCodigo());
        $this->assertEquals('Brasil', $pais->getNome());
    }
}
