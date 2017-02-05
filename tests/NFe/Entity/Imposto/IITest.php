<?php
namespace NFe\Entity\Imposto;

class IITest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(__DIR__))).'/resources';
    }

    public function testIIXML()
    {
        $ii = new \NFe\Entity\Imposto\II();
        $ii->setBase(1000.00);
        $ii->setDespesas(150.00);
        $ii->setValor(100.00);
        $ii->setIOF(80.00);
        $ii->fromArray($ii);
        $ii->fromArray($ii->toArray());
        $ii->fromArray(null);

        $xml = $ii->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/testIIXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/testIIXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIILoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/testIIXML.xml');

        $ii = new \NFe\Entity\Imposto\II();
        $ii->loadNode($dom_cmp->documentElement);

        $xml = $ii->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
