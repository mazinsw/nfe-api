<?php
namespace NFe\Entity\Imposto\IPI;

class QuantidadeTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testQuantidadeXML()
    {
        // Exemplo para Alíquota específica
        $ipi = new \NFe\Entity\Imposto\IPI();
        $ipi_quantidade = new \NFe\Entity\Imposto\IPI\Quantidade();
        $ipi_quantidade->setQuantidade(1000.00);
        $ipi_quantidade->setPreco(0.7640);
        $ipi_quantidade->fromArray($ipi_quantidade);
        $ipi_quantidade->fromArray($ipi_quantidade->toArray());
        $ipi_quantidade->fromArray(null);
        $ipi->setTributo($ipi_quantidade);
        $ipi->fromArray($ipi);
        $ipi->fromArray($ipi->toArray());
        $ipi->fromArray(null);

        $xml = $ipi->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/ipi/testQuantidadeXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/ipi/testQuantidadeXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testQuantidadeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/ipi/testQuantidadeXML.xml');

        $ipi = new \NFe\Entity\Imposto\IPI();
        $ipi->loadNode($dom_cmp->documentElement);

        $xml = $ipi->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
