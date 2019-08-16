<?php
namespace NFe\Entity;

class ImpostoTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadInvalid()
    {
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('invalid'));
        $this->assertFalse(Imposto::loadImposto($dom->documentElement));
    }
}
