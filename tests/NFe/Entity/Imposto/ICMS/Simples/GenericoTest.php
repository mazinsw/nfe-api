<?php
namespace NFe\Entity\Imposto\ICMS\Simples;

class GenericoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/resources';
    }

    public function testGenericoXML()
    {
        $icms_generico = new \NFe\Entity\Imposto\ICMS\Simples\Generico();
        $icms_generico->fromArray($icms_generico);
        $icms_generico->fromArray($icms_generico->toArray());
        $icms_generico->fromArray(null);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testGenericoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/simples/testGenericoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testGenericoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testGenericoXML.xml');

        $icms_generico = new \NFe\Entity\Imposto\ICMS\Simples\Generico();
        $icms_generico->loadNode($dom_cmp->documentElement);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
