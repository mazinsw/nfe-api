<?php
namespace NFe\Entity\Imposto\IPI;

class IsentoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testIsentoXML()
    {
        // Exemplo para operação Não Tributada (só utilize se for contribuinte do IPI)
        $ipi = new \NFe\Entity\Imposto\IPI();
        $ipi_isento = new \NFe\Entity\Imposto\IPI\Isento();
        $ipi_isento->fromArray($ipi_isento);
        $ipi_isento->fromArray($ipi_isento->toArray());
        $ipi_isento->fromArray(null);
        $ipi->setTributo($ipi_isento);
        $ipi->fromArray($ipi);
        $ipi->fromArray($ipi->toArray());
        $ipi->fromArray(null);

        $xml = $ipi->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/ipi/testIsentoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/ipi/testIsentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIsentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/ipi/testIsentoXML.xml');

        $ipi = new \NFe\Entity\Imposto\IPI();
        $ipi->loadNode($dom_cmp->documentElement);

        $xml = $ipi->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
