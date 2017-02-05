<?php
namespace NFe\Entity\Imposto\ICMS;

class IsentoTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;

    protected function setUp()
    {
        $this->resource_path = dirname(dirname(dirname(dirname(__DIR__)))).'/resources';
    }

    public function testIsentoXML()
    {
        $icms_isento = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_isento->fromArray($icms_isento);
        $icms_isento->fromArray($icms_isento->toArray());
        $icms_isento->fromArray(null);

        $xml = $icms_isento->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testIsentoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoXML.xml');

        $icms_isento = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_isento->loadNode($dom_cmp->documentElement);

        $xml = $icms_isento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIsentoCondicionalXML()
    {
        $icms_isento_cond = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_isento_cond->setDesoneracao(1800.00);
        $icms_isento_cond->setMotivo(\NFe\Entity\Imposto\ICMS\Isento::MOTIVO_TAXI);
        $icms_isento_cond->fromArray($icms_isento_cond);
        $icms_isento_cond->fromArray($icms_isento_cond->toArray());
        $icms_isento_cond->fromArray(null);

        $xml = $icms_isento_cond->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoCondicionalXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testIsentoCondicionalXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoCondicionalLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoCondicionalXML.xml');

        $icms_isento_cond = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_isento_cond->loadNode($dom_cmp->documentElement);

        $xml = $icms_isento_cond->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIsentoNaoTributadoXML()
    {
        $icms_nao_trib = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_nao_trib->setTributacao('41');
        $icms_nao_trib->fromArray($icms_nao_trib);
        $icms_nao_trib->fromArray($icms_nao_trib->toArray());
        $icms_nao_trib->fromArray(null);

        $xml = $icms_nao_trib->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoNaoTributadoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testIsentoNaoTributadoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoNaoTributadoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoNaoTributadoXML.xml');

        $icms_nao_trib = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_nao_trib->loadNode($dom_cmp->documentElement);

        $xml = $icms_nao_trib->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testIsentoSuspensaoXML()
    {
        $icms_suspensao = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_suspensao->setTributacao('50');
        $icms_suspensao->fromArray($icms_suspensao);
        $icms_suspensao->fromArray($icms_suspensao->toArray());
        $icms_suspensao->fromArray(null);

        $xml = $icms_suspensao->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoSuspensaoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/imposto/icms/testIsentoSuspensaoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testIsentoSuspensaoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/testIsentoSuspensaoXML.xml');

        $icms_suspensao = new \NFe\Entity\Imposto\ICMS\Isento();
        $icms_suspensao->loadNode($dom_cmp->documentElement);

        $xml = $icms_suspensao->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
