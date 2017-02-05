<?php
namespace NFe\Entity\Imposto\ICMS\Simples;

class IsentoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/resources';
    }

    public function testIsentoXML()
    {
        $icms_isento = new \NFe\Entity\Imposto\ICMS\Simples\Isento();
        $icms_isento->fromArray($icms_isento);
        $icms_isento->fromArray($icms_isento->toArray());
        $icms_isento->fromArray(null);

        $xml = $icms_isento->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testIsentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/simples/testIsentoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testIsentoXML.xml');

        $icms_isento = new \NFe\Entity\Imposto\ICMS\Simples\Isento();
        $icms_isento->loadNode($dom_cmp->documentElement);

        $xml = $icms_isento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
