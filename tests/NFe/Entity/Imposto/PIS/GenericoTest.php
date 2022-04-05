<?php

namespace NFe\Entity\Imposto\PIS;

class GenericoTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp(): void
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testGenericoXML()
    {
        $pis_generico = new Generico();
        $pis_generico->setValor(3.50);
        $pis_generico->fromArray($pis_generico);
        $pis_generico->fromArray($pis_generico->toArray());
        $pis_generico->fromArray(null);

        $xml = $pis_generico->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/pis/testGenericoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testGenericoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testGenericoXML.xml');

        $pis_generico = Generico::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Generico::class, $pis_generico);

        $xml = $pis_generico->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
