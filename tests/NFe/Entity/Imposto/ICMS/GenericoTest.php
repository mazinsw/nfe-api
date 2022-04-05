<?php

namespace NFe\Entity\Imposto\ICMS;

class GenericoTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/resources';
    }

    public function testGenericoXML()
    {
        $icms_generico = new Generico();
        $icms_generico->fromArray($icms_generico);
        $icms_generico->fromArray($icms_generico->toArray());
        $icms_generico->fromArray(null);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testGenericoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoXML.xml');

        $icms_generico = Generico::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Generico::class, $icms_generico);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoReducaoXML()
    {
        $icms_generico = new Generico();
        $icms_generico->getNormal()->setModalidade(Normal::MODALIDADE_OPERACAO);
        $icms_generico->getNormal()->setBase(90.00);
        $icms_generico->getNormal()->setAliquota(18.00);
        $icms_generico->getNormal()->setReducao(10.00);
        $icms_generico->fromArray($icms_generico);
        $icms_generico->fromArray($icms_generico->toArray());
        $icms_generico->fromArray(null);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testGenericoReducaoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoReducaoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoReducaoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoReducaoXML.xml');

        $icms_generico = new Generico();
        $icms_generico->loadNode($dom_cmp->documentElement);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoMargemXML()
    {
        $icms_generico = new Generico();
        $icms_generico->getNormal()->setBase(90.00);
        $icms_generico->getNormal()->setReducao(10.00);
        $icms_generico->getNormal()->setAliquota(18.00);

        $icms_generico->setModalidade(Parcial::MODALIDADE_AGREGADO);
        $icms_generico->setBase(162.00);
        $icms_generico->setMargem(100.00);
        $icms_generico->setReducao(10.00);
        $icms_generico->setAliquota(18.00);
        $icms_generico->fromArray($icms_generico);
        $icms_generico->fromArray($icms_generico->toArray());
        $icms_generico->fromArray(null);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testGenericoMargemXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoMargemXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoMargemLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoMargemXML.xml');

        $icms_generico = new Generico();
        $icms_generico->loadNode($dom_cmp->documentElement);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoModalidadeXML()
    {
        $icms_generico = new Generico();
        $icms_generico->getNormal()->setModalidade(Normal::MODALIDADE_OPERACAO);
        $icms_generico->getNormal()->setBase(90.00);
        $icms_generico->getNormal()->setReducao(10.00);
        $icms_generico->getNormal()->setAliquota(18.00);

        $icms_generico->setModalidade(Parcial::MODALIDADE_AGREGADO);
        $icms_generico->setBase(162.00);
        $icms_generico->setMargem(100.00);
        $icms_generico->setReducao(10.00);
        $icms_generico->setAliquota(18.00);
        $icms_generico->fromArray($icms_generico);
        $icms_generico->fromArray($icms_generico->toArray());
        $icms_generico->fromArray(null);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/testGenericoModalidadeXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoModalidadeXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testGenericoModalidadeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testGenericoModalidadeXML.xml');

        $icms_generico = new Generico();
        $icms_generico->loadNode($dom_cmp->documentElement);

        $xml = $icms_generico->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
