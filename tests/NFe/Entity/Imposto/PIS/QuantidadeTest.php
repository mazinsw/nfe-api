<?php
namespace NFe\Entity\Imposto\PIS;

class QuantidadeTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;
    
    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testQuantidadeXML()
    {
        $pis_quantidade = new \NFe\Entity\Imposto\PIS\Quantidade();
        $pis_quantidade->setQuantidade(1000);
        $pis_quantidade->setAliquota(0.0076);
        $pis_quantidade->fromArray($pis_quantidade);
        $pis_quantidade->fromArray($pis_quantidade->toArray());
        $pis_quantidade->fromArray(null);

        $xml = $pis_quantidade->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testQuantidadeXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/pis/testQuantidadeXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testQuantidadeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testQuantidadeXML.xml');

        $pis_quantidade = new \NFe\Entity\Imposto\PIS\Quantidade();
        $pis_quantidade->loadNode($dom_cmp->documentElement);

        $xml = $pis_quantidade->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
