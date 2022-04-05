<?php

namespace NFe\Entity\Imposto\PIS;

class IsentoTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testIsentoXML()
    {
        $pis_isento = new Isento();
        $pis_isento->setTributacao(Isento::TRIBUTACAO_MONOFASICA);
        $pis_isento->fromArray($pis_isento);
        $pis_isento->fromArray($pis_isento->toArray());
        $pis_isento->fromArray(null);

        $xml = $pis_isento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/pis/testIsentoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testIsentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIsentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testIsentoXML.xml');

        $pis_isento = Isento::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Isento::class, $pis_isento);

        $xml = $pis_isento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
