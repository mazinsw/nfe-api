<?php
namespace NFe\Entity\Imposto\ICMS;

class CobradoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testCobradoXML()
    {
        $icms_cobrado = new \NFe\Entity\Imposto\ICMS\Cobrado();
        $icms_cobrado->setBase(135.00);
        $icms_cobrado->setValor(24.30);
        $icms_cobrado->fromArray($icms_cobrado);
        $icms_cobrado->fromArray($icms_cobrado->toArray());
        $icms_cobrado->fromArray(null);

        $xml = $icms_cobrado->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testCobradoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testCobradoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testCobradoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testCobradoXML.xml');

        $icms_cobrado = new \NFe\Entity\Imposto\ICMS\Cobrado();
        $icms_cobrado->loadNode($dom_cmp->documentElement);

        $xml = $icms_cobrado->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
