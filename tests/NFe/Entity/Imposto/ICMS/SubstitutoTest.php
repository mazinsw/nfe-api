<?php
namespace NFe\Entity\Imposto\ICMS;

class SubstitutoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testSubstitutoXML()
    {
        $icms_substituto = new \NFe\Entity\Imposto\ICMS\Substituto();
        $icms_substituto->getNormal()->setBase(135.00);
        $icms_substituto->getNormal()->setValor(24.30);

        $icms_substituto->setBase(135.00);
        $icms_substituto->setValor(24.30);
        $icms_substituto->fromArray($icms_substituto);
        $icms_substituto->fromArray($icms_substituto->toArray());
        $icms_substituto->fromArray(null);

        $xml = $icms_substituto->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testSubstitutoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testSubstitutoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testSubstitutoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testSubstitutoXML.xml');

        $icms_substituto = new \NFe\Entity\Imposto\ICMS\Substituto();
        $icms_substituto->loadNode($dom_cmp->documentElement);

        $xml = $icms_substituto->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
