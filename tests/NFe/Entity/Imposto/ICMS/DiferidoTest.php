<?php
namespace NFe\Entity\Imposto\ICMS;

class DiferidoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testDiferidoXML()
    {
        $icms_diferido = new \NFe\Entity\Imposto\ICMS\Diferido();
        $icms_diferido->fromArray($icms_diferido);
        $icms_diferido->fromArray($icms_diferido->toArray());
        $icms_diferido->fromArray(null);

        $xml = $icms_diferido->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testDiferidoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testDiferidoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDiferidoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testDiferidoXML.xml');

        $icms_diferido = Diferido::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Diferido::class, $icms_diferido);

        $xml = $icms_diferido->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDiferidoReducaoXML()
    {
        $icms_diferido = new Diferido();
        $icms_diferido->setModalidade(\NFe\Entity\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
        $icms_diferido->setBase(80.00);
        $icms_diferido->setReducao(20.00);
        $icms_diferido->setAliquota(18.00);
        $icms_diferido->setDiferimento(100.00);
        $icms_diferido->fromArray($icms_diferido);
        $icms_diferido->fromArray($icms_diferido->toArray());
        $icms_diferido->fromArray(null);

        $xml = $icms_diferido->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testDiferidoReducaoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testDiferidoReducaoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDiferidoReducaoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testDiferidoReducaoXML.xml');

        $icms_diferido = new Diferido();
        $icms_diferido->loadNode($dom_cmp->documentElement);

        $xml = $icms_diferido->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDiferidoDiferimentoXML()
    {
        $icms_diferido = new \NFe\Entity\Imposto\ICMS\Diferido();
        $icms_diferido->setModalidade(\NFe\Entity\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
        $icms_diferido->setBase(1000.00);
        $icms_diferido->setAliquota(18.00);
        $icms_diferido->setDiferimento(33.3333);
        $icms_diferido->fromArray($icms_diferido);
        $icms_diferido->fromArray($icms_diferido->toArray());
        $icms_diferido->fromArray(null);

        $xml = $icms_diferido->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testDiferidoDiferimentoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testDiferidoDiferimentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDiferidoDiferimentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testDiferidoDiferimentoXML.xml');

        $icms_diferido = new \NFe\Entity\Imposto\ICMS\Diferido();
        $icms_diferido->loadNode($dom_cmp->documentElement);

        $xml = $icms_diferido->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
