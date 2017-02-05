<?php
namespace NFe\Entity\Imposto\ICMS\Simples;

class ParcialTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/resources';
    }

    public function testParcialXML()
    {
        // TODO: verificar vICMSST = 12.96
        $icms_parcial = new \NFe\Entity\Imposto\ICMS\Simples\Parcial();
        $icms_parcial->setModalidade(\NFe\Entity\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
        $icms_parcial->setBase(162.00);
        $icms_parcial->setMargem(100.00);
        $icms_parcial->setReducao(10.00);
        $icms_parcial->setAliquota(18.00);
        $icms_parcial->fromArray($icms_parcial);
        $icms_parcial->fromArray($icms_parcial->toArray());
        $icms_parcial->fromArray(null);

        $xml = $icms_parcial->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testParcialXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/simples/testParcialXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testParcialLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testParcialXML.xml');

        $icms_parcial = new \NFe\Entity\Imposto\ICMS\Simples\Parcial();
        $icms_parcial->loadNode($dom_cmp->documentElement);

        $xml = $icms_parcial->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
