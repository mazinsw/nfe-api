<?php
namespace NFe\Core;

class NFCeTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance();
        $this->sefaz->getConfiguracao()
            ->setArquivoChavePublica(dirname(dirname(__DIR__)) . '/resources/certs/public.pem')
            ->setArquivoChavePrivada(dirname(dirname(__DIR__)) . '/resources/certs/private.pem');
    }

    private function createNFCe()
    {
        $nfce = new \NFe\Core\NFCe();
        $nfce->setCodigo('77882192');
        $nfce->setSerie('1');
        $nfce->setNumero('81');
        $nfce->setDataEmissao(strtotime('2016-09-16T21:36:03-03:00'));
        $nfce->setPresenca(\NFe\Core\Nota::PRESENCA_PRESENCIAL);

        /* Emitente */
        $emitente = new \NFe\Entity\Emitente();
        $emitente->setRazaoSocial('Empresa LTDA');
        $emitente->setFantasia('Minha Empresa');
        $emitente->setCNPJ('08380787000176');
        $emitente->setTelefone('11955886644');
        $emitente->setIE('123456789');
        $emitente->setIM('98765');
        $emitente->setRegime(\NFe\Entity\Emitente::REGIME_SIMPLES);

        $endereco = new \NFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
                 ->setNome('Paranavaí')
                 ->getEstado()
                 ->setUF('PR');
        $endereco->setBairro('Centro');
        $endereco->setLogradouro('Rua Paranavaí');
        $endereco->setNumero('123');
        $endereco->fromArray($endereco);
        $endereco->fromArray($endereco->toArray());
        $endereco->fromArray(null);

        $emitente->setEndereco($endereco);
        $emitente->fromArray($emitente);
        $emitente->fromArray($emitente->toArray());
        $emitente->fromArray(null);
        $this->sefaz->getConfiguracao()->setEmitente($emitente);
        $nfce->setEmitente($emitente);

        /* Destinatário */
        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->setNome('Fulano da Silva');
        $destinatario->setCPF('12345678912');
        $destinatario->setEmail('fulano@site.com.br');
        $destinatario->setTelefone('11988220055');

        $endereco = new \NFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
                 ->setNome('Paranavaí')
                 ->getEstado()
                 ->setUF('PR');
        $endereco->setBairro('Centro');
        $endereco->setLogradouro('Rua Paranavaí');
        $endereco->setNumero('123');
        $endereco->fromArray($endereco);
        $endereco->fromArray($endereco->toArray());
        $endereco->fromArray(null);

        $destinatario->setEndereco($endereco);
        $destinatario->fromArray($destinatario);
        $destinatario->fromArray($destinatario->toArray());
        $destinatario->fromArray(null);
        $nfce->setDestinatario($destinatario);

        /* Produtos */
        $produto = new \NFe\Entity\Produto();
        $produto->setCodigo(123456);
        $produto->setCodigoBarras('7894900011531');
        $produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
        $produto->setUnidade(\NFe\Entity\Produto::UNIDADE_UNIDADE);
        $produto->setPreco(4.99);
        $produto->setQuantidade(1);
        $produto->setNCM('22021000');
        $produto->setCEST('0300700');
        $produto->setCFOP('5405');
        $nfce->addProduto($produto);

        /* Impostos */
        $imposto = new \NFe\Entity\Imposto\ICMS\Cobrado();
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \NFe\Entity\Imposto\PIS\Aliquota();
        $imposto->setTributacao(\NFe\Entity\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(0.65);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \NFe\Entity\Imposto\COFINS\Aliquota();
        $imposto->setTributacao(\NFe\Entity\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(3.00);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);
        $produto->fromArray($produto);
        $produto->fromArray($produto->toArray());
        $produto->fromArray(null);

        $produto = new \NFe\Entity\Produto();
        $produto->setCodigo(123456);
        $produto->setCodigoBarras('7894900011523');
        $produto->setDescricao('REFRIGERANTE FANTA LARANJA 2L');
        $produto->setUnidade(\NFe\Entity\Produto::UNIDADE_UNIDADE);
        $produto->setPreco(9.00);
        $produto->setQuantidade(2);
        $produto->setDesconto(2.20);
        $produto->setNCM('22021000');
        $produto->setCEST('0300700');
        $produto->setCFOP('5405');
        $nfce->addProduto($produto);

        /* Impostos */
        $imposto = new \NFe\Entity\Imposto\ICMS\Cobrado();
        $imposto->setBase(0.00);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \NFe\Entity\Imposto\PIS\Aliquota();
        $imposto->setTributacao(\NFe\Entity\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(0.65);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \NFe\Entity\Imposto\COFINS\Aliquota();
        $imposto->setTributacao(\NFe\Entity\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(3.00);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);
        $produto->fromArray($produto);
        $produto->fromArray($produto->toArray());
        $produto->fromArray(null);

        /* Pagamentos */
        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_CREDITO);
        $pagamento->setValor(4.50);
        $pagamento->setIntegrado('N');
        $pagamento->setBandeira(\NFe\Entity\Pagamento::BANDEIRA_MASTERCARD);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $nfce->addPagamento($pagamento);

        $pagamento = new \NFe\Entity\Pagamento();
        $pagamento->setForma(\NFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(9.49);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $nfce->addPagamento($pagamento);
        
        $nfce->fromArray($nfce);
        $nfce->fromArray($nfce->toArray());
        $nfce->fromArray(null);
        return $nfce;
    }

    public function testNFCeXML()
    {
        $nfce = $this->createNFCe();
        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeXML.xml');
        $xml_cmp = $dom_cmp->saveXML();
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML());

        // $dom->formatOutput = true;
        // file_put_contents(dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeXML.xml', $dom->saveXML());
    }

    public function testNFCeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeXML.xml');

        $nfce = new \NFe\Core\NFCe();
        $nfce->loadNode($dom_cmp->documentElement);

        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML();
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML());
    }

    public function testNFCeAssinadaXML()
    {
        $nfce = $this->createNFCe();
        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;
        $dom = $nfce->assinar($dom);

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeAssinadaXML.xml');
        $xml_cmp = $dom_cmp->saveXML();
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML());

        // $dom->formatOutput = true;
        // file_put_contents(
        //     dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeAssinadaXML.xml',
        //     $dom->saveXML()
        // );
    }

    public function testNFCeAssinadaLoadXML()
    {
        $xml_file = dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeAssinadaXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $nfce = new \NFe\Core\NFCe();
        $nfce->load($xml_file);
        $dom = $nfce->assinar(); // O carregamento (load) não carrega assinatura

        $xml_cmp = $dom_cmp->saveXML();
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML());
    }

    public function testNFCeValidarXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeAssinadaXML.xml');

        $nfce = new \NFe\Core\NFCe();
        $nfce->loadNode($dom_cmp->documentElement);

        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;
        $dom = $nfce->assinar($dom); // O carregamento (loadNode) não carrega assinatura
        $dom = $nfce->validar($dom);
    }
}
