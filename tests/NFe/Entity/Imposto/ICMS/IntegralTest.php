<?php
namespace NFe\Entity\Imposto\ICMS;

class IntegralTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testIntegralXML()
    {
        $icms_integral = new \NFe\Entity\Imposto\ICMS\Integral();
        $icms_integral->setModalidade(\NFe\Entity\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
        $icms_integral->setBase(588.78);
        $icms_integral->setAliquota(18.00);
        $icms_integral->fromArray($icms_integral);
        $icms_integral->fromArray($icms_integral->toArray());
        $icms_integral->fromArray(null);

        $xml = $icms_integral->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIntegralXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testIntegralXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIntegralLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIntegralXML.xml');

        $icms_integral = new \NFe\Entity\Imposto\ICMS\Integral();
        $icms_integral->loadNode($dom_cmp->documentElement);

        $xml = $icms_integral->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
