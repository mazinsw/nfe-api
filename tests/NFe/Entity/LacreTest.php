<?php
namespace NFe\Entity;

class LacreTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testLacre()
    {
        $lacre = new \NFe\Entity\Lacre();
        $lacre->setNumero(123);
        $lacre->fromArray($lacre);
        $lacre->fromArray($lacre->toArray());
        $lacre->fromArray(null);

        $this->assertEquals(123, $lacre->getNumero());
    }
}
