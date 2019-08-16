<?php
namespace NFe\Entity\Imposto\ICMS;

class PartilhaTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testPartilhaXML()
    {
        $icms_partilha = new Partilha();
        $icms_partilha->getNormal()->setModalidade(Normal::MODALIDADE_OPERACAO);
        $icms_partilha->getNormal()->setBase(90.00);
        $icms_partilha->getNormal()->setReducao(10.00);
        $icms_partilha->getNormal()->setAliquota(18.00);

        $icms_partilha->setModalidade(Parcial::MODALIDADE_AGREGADO);
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

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testPartilhaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testPartilhaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPartilhaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testPartilhaXML.xml');

        $icms_partilha = Partilha::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Partilha::class, $icms_partilha);

        $xml = $icms_partilha->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
