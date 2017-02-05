<?php
namespace NFe\Entity\Imposto\PIS;

class IsentoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;
    
    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testIsentoXML()
    {
        $pis_isento = new \NFe\Entity\Imposto\PIS\Isento();
        $pis_isento->setTributacao(\NFe\Entity\Imposto\PIS\Isento::TRIBUTACAO_MONOFASICA);
        $pis_isento->fromArray($pis_isento);
        $pis_isento->fromArray($pis_isento->toArray());
        $pis_isento->fromArray(null);

        $xml = $pis_isento->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testIsentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/pis/testIsentoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testIsentoXML.xml');

        $pis_isento = new \NFe\Entity\Imposto\PIS\Isento();
        $pis_isento->loadNode($dom_cmp->documentElement);

        $xml = $pis_isento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
