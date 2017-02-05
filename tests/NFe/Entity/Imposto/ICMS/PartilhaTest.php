<?php
namespace NFe\Entity\Imposto\ICMS;

class PartilhaTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testPartilhaXML()
    {
        $icms_partilha = new \NFe\Entity\Imposto\ICMS\Partilha();
        $icms_partilha->getNormal()->setModalidade(\NFe\Entity\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
        $icms_partilha->getNormal()->setBase(90.00);
        $icms_partilha->getNormal()->setReducao(10.00);
        $icms_partilha->getNormal()->setAliquota(18.00);

        $icms_partilha->setModalidade(\NFe\Entity\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
        $icms_partilha->setBase(162.00);
        $icms_partilha->setMargem(100.00);
        $icms_partilha->setReducao(10.00);
        $icms_partilha->setAliquota(18.00);
        $icms_partilha->setOperacao(5.70);
        $icms_partilha->setUF('PR');
        $icms_partilha->fromArray($icms_partilha);
        $icms_partilha->fromArray($icms_partilha->toArray());
        $icms_partilha->fromArray(null);

        $xml = $icms_partilha->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testPartilhaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testPartilhaXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testPartilhaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testPartilhaXML.xml');

        $icms_partilha = new \NFe\Entity\Imposto\ICMS\Partilha();
        $icms_partilha->loadNode($dom_cmp->documentElement);

        $xml = $icms_partilha->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
