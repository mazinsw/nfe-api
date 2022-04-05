<?php

namespace NFe\Entity;

class EnderecoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
    }

    public static function createEndereco()
    {
        $endereco = new \NFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
                 ->setNome('Paranavaí')
                 ->getEstado()
                 ->setUF('PR');
        $endereco->setBairro('Centro');
        $endereco->setLogradouro('Rua Paranavaí');
        $endereco->setNumero('123');
        $endereco->setComplemento('Loteamento Paranavaí');
        return $endereco;
    }

    public function testEnderecoXML()
    {
        $endereco = new \NFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
                 ->setNome('Paranavaí')
                 ->getEstado()
                 ->setUF('PR');
        $endereco->setBairro('Centro');
        $endereco->setLogradouro('Rua Paranavaí');
        $endereco->setNumero('123');
        $endereco->setComplemento('Loteamento Paranavaí');
        $endereco->fromArray($endereco);
        $endereco->fromArray($endereco->toArray());
        $endereco->fromArray(null);

        $xml = $endereco->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/endereco/testEnderecoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/endereco/testEnderecoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testEnderecoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/endereco/testEnderecoXML.xml');

        $endereco = new \NFe\Entity\Endereco();
        $endereco->loadNode($dom_cmp->documentElement);

        $xml = $endereco->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
