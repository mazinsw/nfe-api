<?php
namespace NFe\Entity;

class DestinatarioTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
    }

    public function testDestinatarioFisicoXML()
    {
        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->setNome('Fulano da Silva');
        $destinatario->setCPF('12345678912');
        $destinatario->setEmail('fulano@site.com.br');
        $destinatario->setTelefone('11988220055');

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

        $destinatario->setEndereco($endereco);
        $destinatario->fromArray($destinatario);
        $destinatario->fromArray($destinatario->toArray());
        $destinatario->fromArray(null);

        $xml = $destinatario->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioFisicoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioFisicoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDestinatarioFisicoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioFisicoXML.xml');

        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->loadNode($dom_cmp->documentElement);

        $xml = $destinatario->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDestinatarioJuridicoXML()
    {
        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->setRazaoSocial('Empresa LTDA');
        $destinatario->setCNPJ('12345678000123');
        $destinatario->setEmail('contato@empresa.com.br');
        $destinatario->setIE('123456789');

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

        $destinatario->setEndereco($endereco);
        $destinatario->fromArray($destinatario);
        $destinatario->fromArray($destinatario->toArray());
        $destinatario->fromArray(null);

        $xml = $destinatario->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioJuridicoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioJuridicoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDestinatarioJuridicoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioJuridicoXML.xml');

        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->loadNode($dom_cmp->documentElement);

        $xml = $destinatario->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDestinatarioCPFnaNotaXML()
    {
        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->setCPF('12345678912');
        $destinatario->setEndereco(null);
        $destinatario->fromArray($destinatario);
        $destinatario->fromArray($destinatario->toArray());
        $destinatario->fromArray(null);

        $xml = $destinatario->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioCPFnaNotaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioCPFnaNotaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testDestinatarioCPFnaNotaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)).'/resources/xml/destinatario/testDestinatarioCPFnaNotaXML.xml');

        $destinatario = new \NFe\Entity\Destinatario();
        $destinatario->loadNode($dom_cmp->documentElement);

        $xml = $destinatario->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
