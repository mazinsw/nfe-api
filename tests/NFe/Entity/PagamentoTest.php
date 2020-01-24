<?php
namespace NFe\Entity;

class PagamentoTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testIndicador()
    {
        $pagamento = new Pagamento();
        $pagamento->setIndicador('0');
        $this->assertEquals(Pagamento::INDICADOR_AVISTA, $pagamento->getIndicador());
        $this->assertEquals('0', $pagamento->getIndicador(true));
        $pagamento->setIndicador('1');
        $this->assertEquals(Pagamento::INDICADOR_APRAZO, $pagamento->getIndicador());
        $this->assertEquals('1', $pagamento->getIndicador(true));
        $pagamento->setIndicador('2');
        $this->assertEquals('2', $pagamento->getIndicador());
        $this->assertEquals('2', $pagamento->getIndicador(true));
    }

    public function testPagamentoDinheiroXML()
    {
        $pagamento = new Pagamento();
        $pagamento->setForma(Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(8.10);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoDinheiroXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoDinheiroXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoDinheiroLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoDinheiroXML.xml');

        $pagamento = new Pagamento();
        $pagamento->loadNode($dom_cmp->documentElement);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoTrocoXML()
    {
        $pagamento = new Pagamento();
        $pagamento->setForma(Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(-8.10);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoTrocoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoTrocoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoTrocoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoTrocoXML.xml');

        $pagamento = new Pagamento();
        $pagamento->loadNode($dom_cmp, 'vTroco');

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoXML()
    {
        $pagamento = new Pagamento();
        $pagamento->setForma(Pagamento::FORMA_CREDITO);
        $pagamento->setValor(4.50);
        $pagamento->setIntegrado('Y');
        $pagamento->setCredenciadora('60889128000422');
        $pagamento->setBandeira(Pagamento::BANDEIRA_MASTERCARD);
        $pagamento->setAutorizacao('110011');
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoCartaoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoCartaoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoCartaoXML.xml');

        $pagamento = new Pagamento();
        $pagamento->loadNode($dom_cmp->documentElement);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoNaoIntegradoXML()
    {
        $pagamento = new Pagamento();
        $pagamento->setForma(Pagamento::FORMA_DEBITO);
        $pagamento->setValor(4.50);
        $pagamento->setIntegrado('N');
        $pagamento->setBandeira(Pagamento::BANDEIRA_VISA);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoCartaoNaoIntegradoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoCartaoNaoIntegradoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoNaoIntegradoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/pagamento/testPagamentoCartaoNaoIntegradoXML.xml');

        $pagamento = new Pagamento();
        $pagamento->loadNode($dom_cmp->documentElement);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
