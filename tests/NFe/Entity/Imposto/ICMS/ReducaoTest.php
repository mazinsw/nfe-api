<?php

namespace NFe\Entity\Imposto\ICMS;

class ReducaoTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp(): void
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testReducaoXML()
    {
        $icms_reducao = new Reducao();
        $icms_reducao->setModalidade(Normal::MODALIDADE_OPERACAO);
        $icms_reducao->setBase(90.00);
        $icms_reducao->setAliquota(18.0);
        $icms_reducao->setReducao(10.0);
        $icms_reducao->fromArray($icms_reducao);
        $icms_reducao->fromArray($icms_reducao->toArray());
        $icms_reducao->fromArray(null);

        $xml = $icms_reducao->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testReducaoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testReducaoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testReducaoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testReducaoXML.xml');

        $icms_reducao = Reducao::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Reducao::class, $icms_reducao);

        $xml = $icms_reducao->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
