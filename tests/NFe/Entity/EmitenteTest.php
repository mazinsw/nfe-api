<?php

namespace NFe\Entity;

class EmitenteTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
    }

    public static function createEmitente()
    {
        $emitente = new \NFe\Entity\Emitente();
        $emitente->setRazaoSocial('Empresa LTDA');
        $emitente->setFantasia('Minha Empresa');
        $emitente->setCNPJ('08380787000176');
        $emitente->setTelefone('11955886644');
        $emitente->setIE('123456789');
        $emitente->setIM('98765');
        $emitente->setRegime(\NFe\Entity\Emitente::REGIME_SIMPLES);

        $endereco = EnderecoTest::createEndereco();
        $emitente->setEndereco($endereco);
        return $emitente;
    }

    public function testEmitenteXML()
    {
        $emitente = new \NFe\Entity\Emitente();
        $emitente->setRazaoSocial('Empresa LTDA');
        $emitente->setFantasia('Minha Empresa');
        $emitente->setCNPJ('12345678000123');
        $emitente->setTelefone('11955886644');
        $emitente->setIE('123456789');
        $emitente->setIM('95656');
        $emitente->setRegime(\NFe\Entity\Emitente::REGIME_SIMPLES);

        $endereco = new \NFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
                 ->setNome('Paranavaí')
                 ->setCodigo(123456)
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

        $xml = $emitente->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/emitente/testEmitenteXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/emitente/testEmitenteXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testEmitenteLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/emitente/testEmitenteXML.xml');

        $emitente = new \NFe\Entity\Emitente();
        $emitente->loadNode($dom_cmp->documentElement);

        $xml = $emitente->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
