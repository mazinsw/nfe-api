<?php

namespace NFe\Entity;

class PesoTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
    }

    public function testPeso()
    {
        $peso = new \NFe\Entity\Peso();
        $peso->setLiquido(15.0);
        $peso->setBruto(21.0);
        $peso->fromArray($peso);
        $peso->fromArray($peso->toArray());
        $peso->fromArray(null);

        $this->assertEquals(15.0, $peso->getLiquido());
        $this->assertEquals(21.0, $peso->getBruto());
    }
}
