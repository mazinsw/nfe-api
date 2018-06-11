<?php
namespace NFe\Core;

class NFeTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = SEFAZTest::createSEFAZ();
    }

    private function createNFe()
    {
        $nfe = new NFe();
        $nfe->setCodigo('77882192');
        $nfe->setSerie('1');
        $nfe->setNumero('81');
        $nfe->setDataEmissao(strtotime('2016-09-16T21:36:03-03:00'));
        $nfe->setPresenca(Nota::PRESENCA_PRESENCIAL);
        $nfe->addObservacao('Vendedor', 'Fulano de Tal');
        $nfe->addObservacao('Local', 'Mesa 02');
        $nfe->addInformacao('RegimeEspecial', '123456');

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
        $nfe->setEmitente($emitente);

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
        $nfe->setDestinatario($destinatario);

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
        $nfe->addProduto($produto);

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
        $nfe->addProduto($produto);

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
        $nfe->fromArray($nfe);
        $nfe->fromArray($nfe->toArray());
        $nfe->fromArray(null);
        return $nfe;
    }

    public static function loadNFeXML()
    {
        $xml_file = dirname(dirname(__DIR__)).'/resources/xml/nota/testNFeXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);
        return $dom_cmp;
    }

    public function testNFeXML()
    {
        $nfe = $this->createNFe();
        $xml = $nfe->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(dirname(dirname(__DIR__)).'/resources/xml/nota/testNFeXML.xml', $dom->saveXML());
        }

        $dom_cmp = self::loadNFeXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }
}
