<?php
namespace NFe\Entity\Imposto\PIS;

class AliquotaTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;
    
    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testAliquotaXML()
    {
        $pis_aliquota = new \NFe\Entity\Imposto\PIS\Aliquota();
        $pis_aliquota->setTributacao(\NFe\Entity\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
        $pis_aliquota->setBase(883.12);
        $pis_aliquota->setAliquota(1.65);
        $pis_aliquota->fromArray($pis_aliquota);
        $pis_aliquota->fromArray($pis_aliquota->toArray());
        $pis_aliquota->fromArray(null);

        $xml = $pis_aliquota->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/pis/testAliquotaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testAliquotaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testAliquotaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/pis/testAliquotaXML.xml');

        $pis_aliquota = new \NFe\Entity\Imposto\PIS\Aliquota();
        $pis_aliquota->loadNode($dom_cmp->documentElement);

        $xml = $pis_aliquota->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
