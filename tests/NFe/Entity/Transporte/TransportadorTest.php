<?php
namespace NFe\Entity\Transporte;

class TransportadorTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testTransportadorXML()
    {
        $transportador = new \NFe\Entity\Transporte\Transportador();
        $transportador->setRazaoSocial('Empresa LTDA');
        $transportador->setCNPJ('12345678000123');
        $transportador->setIE('123456789');

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

        $transportador->setEndereco($endereco);
        $transportador->fromArray($transportador);
        $transportador->fromArray($transportador->toArray());
        $transportador->fromArray(null);

        $xml = $transportador->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(dirname(__DIR__))) . '/resources/xml/transportador/testTransportadorXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            dirname(dirname(dirname(__DIR__))) . '/resources/xml/transportador/testTransportadorXML.xml'
        );
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransportadorLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            dirname(dirname(dirname(__DIR__))) . '/resources/xml/transportador/testTransportadorXML.xml'
        );

        $transportador = new \NFe\Entity\Transporte\Transportador();
        $transportador->loadNode($dom_cmp->documentElement);

        $xml = $transportador->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransportadorSemEnderecoXML()
    {
        $transportador = new \NFe\Entity\Transporte\Transportador();
        $transportador->setRazaoSocial('Empresa LTDA');
        $transportador->setCNPJ('12345678000123');
        $transportador->setIE('123456789');
        $transportador->setEndereco(null);

        $xml = $transportador->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(dirname(__DIR__))) . '/resources/xml/transportador/testTransportadorSemEnderecoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            dirname(dirname(dirname(__DIR__))) . '/resources/xml/transportador/testTransportadorSemEnderecoXML.xml'
        );
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransportadorSemEnderecoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            dirname(dirname(dirname(__DIR__))) . '/resources/xml/transportador/testTransportadorSemEnderecoXML.xml'
        );

        $transportador = new \NFe\Entity\Transporte\Transportador();
        $transportador->loadNode($dom_cmp->documentElement);

        $xml = $transportador->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
