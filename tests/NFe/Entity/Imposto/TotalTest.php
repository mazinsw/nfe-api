<?php

namespace NFe\Entity\Imposto;

class TotalTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(__DIR__))) . '/resources';
    }

    public function testTotalXML()
    {
        $total = new \NFe\Entity\Imposto\Total();
        $total->setBase(883.12);
        $total->setAliquota(100.00);
        $total->fromArray($total);
        $total->fromArray($total->toArray());
        $total->fromArray(null);

        $xml = $total->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/testTotalXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/testTotalXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTotalLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/testTotalXML.xml');

        $total = new \NFe\Entity\Imposto\Total();
        $total->loadNode($dom_cmp->documentElement);

        $xml = $total->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
