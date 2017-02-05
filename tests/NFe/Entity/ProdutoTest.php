<?php
namespace NFe\Entity;

class ProdutoTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $sefaz = \NFe\Core\SEFAZ::getInstance();
        $sefaz->getConfiguracao()->getEmitente()->getEndereco()
                         ->getMunicipio()->getEstado()->setUF('PR');
    }

    public function testProdutoXML()
    {
        $produto = new \NFe\Entity\Produto();
        $produto->setItem(1);
        $produto->setPedido(123);
        $produto->setCodigo(123456);
        $produto->setCodigoBarras('7894900011517');
        $produto->setCodigoTributario('7894900011517');
        $produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
        $produto->setUnidade(\NFe\Entity\Produto::UNIDADE_UNIDADE);
        $produto->setMultiplicador(1);
        $produto->setPreco(4.50);
        $produto->setQuantidade(2);
        $produto->setDesconto(0.20);
        $produto->setSeguro(0.30);
        $produto->setFrete(2.00);
        $produto->setDespesas(0.50);
        $produto->setCFOP('5101');
        $produto->setNCM('22021000');
        $produto->setCEST('0300700');
        /* Impostos */
        $imposto = new \NFe\Entity\Imposto\ICMS\Simples\Cobrado();
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        
        $produto->addImposto($imposto);
        $produto->fromArray($produto);
        $produto->fromArray($produto->toArray());
        $produto->fromArray(null);

        $xml = $produto->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/produto/testProdutoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     dirname(dirname(__DIR__)).'/resources/xml/produto/testProdutoXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testProdutoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/produto/testProdutoXML.xml');

        $produto = new \NFe\Entity\Produto();
        $produto->loadNode($dom_cmp->documentElement);

        $xml = $produto->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
