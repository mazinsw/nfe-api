<?php
namespace NFe\Entity\Imposto\PIS\ST;

class QuantidadeTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;
    
    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/resources';
    }

    public function testQuantidadeXML()
    {
        $pisst_quantidade = new Quantidade();
        $pisst_quantidade->setQuantidade(1000);
        $pisst_quantidade->setAliquota(0.0076);
        $pisst_quantidade->fromArray($pisst_quantidade);
        $pisst_quantidade->fromArray($pisst_quantidade->toArray());
        $pisst_quantidade->fromArray(null);

        $xml = $pisst_quantidade->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/pis/st/testQuantidadeXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/st/testQuantidadeXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testQuantidadeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/st/testQuantidadeXML.xml');

        $pisst_quantidade = Quantidade::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Quantidade::class, $pisst_quantidade);

        $xml = $pisst_quantidade->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
