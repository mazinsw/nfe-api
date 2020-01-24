<?php
namespace NFe\Entity\Imposto\ICMS;

class SubstitutoTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testSubstitutoXML()
    {
        $icms_substituto = new Substituto();
        $icms_substituto->getNormal()->setBase(135.00);
        $icms_substituto->getNormal()->setValor(24.30);

        $icms_substituto->setBase(135.00);
        $icms_substituto->setValor(24.30);
        $icms_substituto->fromArray($icms_substituto);
        $icms_substituto->fromArray($icms_substituto->toArray());
        $icms_substituto->fromArray(null);

        $xml = $icms_substituto->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testSubstitutoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testSubstitutoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testSubstitutoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testSubstitutoXML.xml');

        $icms_substituto = Substituto::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Substituto::class, $icms_substituto);

        $xml = $icms_substituto->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
