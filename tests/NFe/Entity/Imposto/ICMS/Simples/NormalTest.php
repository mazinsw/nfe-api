<?php
namespace NFe\Entity\Imposto\ICMS\Simples;

class NormalTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/resources';
    }

    public function testNormalXML()
    {
        $icms_simples_normal = new \NFe\Entity\Imposto\ICMS\Simples\Normal();
        $icms_simples_normal->setBase(1036.80);
        $icms_simples_normal->setAliquota(1.25);
        $icms_simples_normal->fromArray($icms_simples_normal);
        $icms_simples_normal->fromArray($icms_simples_normal->toArray());
        $icms_simples_normal->fromArray(null);

        $xml = $icms_simples_normal->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testNormalXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/simples/testNormalXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testNormalLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testNormalXML.xml');

        $icms_simples_normal = new \NFe\Entity\Imposto\ICMS\Simples\Normal();
        $icms_simples_normal->loadNode($dom_cmp->documentElement);

        $xml = $icms_simples_normal->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
