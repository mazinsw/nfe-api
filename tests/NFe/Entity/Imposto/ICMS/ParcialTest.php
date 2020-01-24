<?php
namespace NFe\Entity\Imposto\ICMS;

class ParcialTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testParcialXML()
    {
        $icms_parcial = new Parcial();
        $icms_parcial->setModalidade(Parcial::MODALIDADE_AGREGADO);
        $icms_parcial->setBase(135.00);
        $icms_parcial->setMargem(50.00);
        $icms_parcial->setReducao(10.00);
        $icms_parcial->setAliquota(18.00);
        $icms_parcial->fromArray($icms_parcial);
        $icms_parcial->fromArray($icms_parcial->toArray());
        $icms_parcial->fromArray(null);

        $xml = $icms_parcial->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testParcialXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testParcialXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testParcialLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testParcialXML.xml');

        $icms_parcial = Parcial::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Parcial::class, $icms_parcial);

        $xml = $icms_parcial->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testParcialFundoXML()
    {
        $icms_parcial = new Parcial();
        $icms_parcial->setModalidade(Parcial::MODALIDADE_AGREGADO);
        $icms_parcial->setBase(135.00);
        $icms_parcial->setMargem(50.00);
        $icms_parcial->setReducao(10.00);
        $icms_parcial->setAliquota(18.00);
        $icms_parcial->getFundo()->setBase($icms_parcial->getBase());
        $icms_parcial->getFundo()->setAliquota(2.00);
        $icms_parcial->fromArray($icms_parcial);
        $icms_parcial->fromArray($icms_parcial->toArray());
        $icms_parcial->fromArray(null);

        $xml = $icms_parcial->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testParcialFundoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testParcialFundoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testParcialFundoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testParcialFundoXML.xml');

        $icms_parcial = new Parcial();
        $icms_parcial->loadNode($dom_cmp->documentElement);

        $xml = $icms_parcial->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
