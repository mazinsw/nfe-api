<?php
namespace NFe\Entity\Imposto\ICMS;

class CobrancaTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testCobrancaXML()
    {
        $icms_cobranca = new Cobranca();
        $icms_cobranca->getNormal()->setModalidade(\NFe\Entity\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
        $icms_cobranca->getNormal()->setBase(100.00);
        $icms_cobranca->getNormal()->setAliquota(18.00);
        $icms_cobranca->setModalidade(\NFe\Entity\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
        $icms_cobranca->setBase(135.00);
        $icms_cobranca->setMargem(50.00);
        $icms_cobranca->setReducao(10.00);
        $icms_cobranca->setAliquota(18.00);
        $icms_cobranca->fromArray($icms_cobranca);
        $icms_cobranca->fromArray($icms_cobranca->toArray());
        $icms_cobranca->fromArray(null);

        $xml = $icms_cobranca->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testCobrancaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testCobrancaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testCobrancaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testCobrancaXML.xml');

        $icms_cobranca = Cobranca::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Cobranca::class, $icms_cobranca);

        $xml = $icms_cobranca->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
