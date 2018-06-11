<?php
namespace NFe\Entity;

class PagamentoTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testPagamentoDinheiroXML()
    {
        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(8.10);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoDinheiroXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoDinheiroXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoDinheiroLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoDinheiroXML.xml');

        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->loadNode($dom_cmp->documentElement);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoTrocoXML()
    {
        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(-8.10);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoTrocoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoTrocoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoTrocoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoTrocoXML.xml');

        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->loadNode($dom_cmp, 'vTroco');

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoXML()
    {
        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_CREDITO);
        $pagamento->setValor(4.50);
        $pagamento->setIntegrado('Y');
        $pagamento->setCredenciadora('60889128000422');
        $pagamento->setBandeira(\NFe\Entity\Pagamento::BANDEIRA_MASTERCARD);
        $pagamento->setAutorizacao('110011');
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoCartaoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoCartaoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoCartaoXML.xml');

        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->loadNode($dom_cmp->documentElement);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoNaoIntegradoXML()
    {
        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_DEBITO);
        $pagamento->setValor(4.50);
        $pagamento->setIntegrado('N');
        $pagamento->setBandeira(\NFe\Entity\Pagamento::BANDEIRA_VISA);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoCartaoNaoIntegradoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoCartaoNaoIntegradoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testPagamentoCartaoNaoIntegradoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/pagamento/testPagamentoCartaoNaoIntegradoXML.xml');

        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->loadNode($dom_cmp->documentElement);

        $xml = $pagamento->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
