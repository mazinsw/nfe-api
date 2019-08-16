<?php
namespace NFe\Entity\Imposto\PIS\ST;

class AliquotaTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;
    
    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/resources';
    }

    public function testAliquotaXML()
    {
        $pisst_aliquota = new Aliquota();
        $pisst_aliquota->setTributacao(Aliquota::TRIBUTACAO_NORMAL);
        $pisst_aliquota->setBase(100.00);
        $pisst_aliquota->setAliquota(0.65);
        $pisst_aliquota->fromArray($pisst_aliquota);
        $pisst_aliquota->fromArray($pisst_aliquota->toArray());
        $pisst_aliquota->fromArray(null);

        $xml = $pisst_aliquota->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/pis/st/testAliquotaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/st/testAliquotaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testAliquotaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/st/testAliquotaXML.xml');

        $pisst_aliquota = Aliquota::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Aliquota::class, $pisst_aliquota);

        $xml = $pisst_aliquota->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
