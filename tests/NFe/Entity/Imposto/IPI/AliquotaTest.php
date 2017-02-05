<?php
namespace NFe\Entity\Imposto\IPI;

class AliquotaTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testAliquotaXML()
    {
        // Exemplo para Alíquota ad valorem
        $ipi = new \NFe\Entity\Imposto\IPI();
        $ipi_aliquota = new \NFe\Entity\Imposto\IPI\Aliquota();
        $ipi_aliquota->setBase(1000.00);
        $ipi_aliquota->setAliquota(7.00);
        $ipi_aliquota->fromArray($ipi_aliquota);
        $ipi_aliquota->fromArray($ipi_aliquota->toArray());
        $ipi_aliquota->fromArray(null);
        $ipi->setTributo($ipi_aliquota);
        $ipi->fromArray($ipi);
        $ipi->fromArray($ipi->toArray());
        $ipi->fromArray(null);

        $xml = $ipi->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/ipi/testAliquotaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/ipi/testAliquotaXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testAliquotaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/ipi/testAliquotaXML.xml');

        $ipi = new \NFe\Entity\Imposto\IPI();
        $ipi->loadNode($dom_cmp->documentElement);

        $xml = $ipi->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
