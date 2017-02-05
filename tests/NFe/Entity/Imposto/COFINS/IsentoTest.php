<?php
namespace NFe\Entity\Imposto\COFINS;

class IsentoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testIsentoXML()
    {
        $cofins_isento = new \NFe\Entity\Imposto\COFINS\Isento();
        $cofins_isento->setTributacao(\NFe\Entity\Imposto\COFINS\Isento::TRIBUTACAO_MONOFASICA);
        $cofins_isento->fromArray($cofins_isento);
        $cofins_isento->fromArray($cofins_isento->toArray());
        $cofins_isento->fromArray(null);

        $xml = $cofins_isento->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/cofins/testIsentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/cofins/testIsentoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/cofins/testIsentoXML.xml');

        $cofins_isento = new \NFe\Entity\Imposto\COFINS\Isento();
        $cofins_isento->loadNode($dom_cmp->documentElement);

        $xml = $cofins_isento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
