<?php

namespace NFe\Entity\Imposto\ICMS;

class IntegralTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp(): void
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testIntegralXML()
    {
        $icms_integral = new Integral();
        $icms_integral->setModalidade(Normal::MODALIDADE_OPERACAO);
        $icms_integral->setBase(588.78);
        $icms_integral->setAliquota(18.00);
        $icms_integral->fromArray($icms_integral);
        $icms_integral->fromArray($icms_integral->toArray());
        $icms_integral->fromArray(null);

        $xml = $icms_integral->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testIntegralXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIntegralXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIntegralLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIntegralXML.xml');

        $icms_integral = Integral::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Integral::class, $icms_integral);

        $xml = $icms_integral->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIntegralFundoXML()
    {
        $icms_integral = new Integral();
        $icms_integral->setModalidade(Normal::MODALIDADE_OPERACAO);
        $icms_integral->setBase(588.78);
        $icms_integral->setAliquota(18.00);
        $icms_integral->getFundo()->setAliquota(2.00);
        $icms_integral->fromArray($icms_integral);
        $icms_integral->fromArray($icms_integral->toArray(true));
        $icms_integral->fromArray(null);

        $xml = $icms_integral->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testIntegralFundoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIntegralFundoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIntegralLoadFundoXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIntegralFundoXML.xml');

        $icms_integral = new Integral();
        $icms_integral->loadNode($dom_cmp->documentElement);

        $xml = $icms_integral->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
