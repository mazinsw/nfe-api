<?php
namespace NFe\Entity\Imposto\COFINS\ST;

class AliquotaTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/resources';
    }

    public function testAliquotaXML()
    {
        $cofins_aliquota = new Aliquota();
        $cofins_aliquota->setTributacao(Aliquota::TRIBUTACAO_NORMAL);
        $cofins_aliquota->setBase(100.00);
        $cofins_aliquota->setAliquota(3.00);
        $cofins_aliquota->fromArray($cofins_aliquota);
        $cofins_aliquota->fromArray($cofins_aliquota->toArray());
        $cofins_aliquota->fromArray(null);

        $xml = $cofins_aliquota->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/cofins/st/testAliquotaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/cofins/st/testAliquotaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testAliquotaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/cofins/st/testAliquotaXML.xml');

        $cofins_aliquota = Aliquota::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Aliquota::class, $cofins_aliquota);

        $xml = $cofins_aliquota->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
