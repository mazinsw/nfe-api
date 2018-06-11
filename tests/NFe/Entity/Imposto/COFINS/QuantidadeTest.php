<?php
namespace NFe\Entity\Imposto\COFINS;

class QuantidadeTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testQuantidadeXML()
    {
        $cofins_quantidade = new Quantidade();
        $cofins_quantidade->setQuantidade(1000);
        $cofins_quantidade->setAliquota(0.0076);
        $cofins_quantidade->fromArray($cofins_quantidade);
        $cofins_quantidade->fromArray($cofins_quantidade->toArray());
        $cofins_quantidade->fromArray(null);

        $xml = $cofins_quantidade->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/cofins/testQuantidadeXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/cofins/testQuantidadeXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testQuantidadeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/cofins/testQuantidadeXML.xml');

        $cofins_quantidade = Quantidade::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Quantidade::class, $cofins_quantidade);

        $xml = $cofins_quantidade->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
