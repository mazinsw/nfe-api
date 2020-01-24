<?php
namespace NFe\Entity;

use NFe\Common\Util;

class ResponsavelTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
    }

    public function testResponsavelXML()
    {
        $responsavel = new \NFe\Entity\Responsavel();
        $responsavel->setCNPJ('12345678000123');
        $responsavel->setContato('Empresa LTDA');
        $responsavel->setEmail('contato@empresa.com.br');
        $responsavel->setTelefone('11988220055');
        $responsavel->setIDCsrt(99);
        $responsavel->setHashCsrt('aWv6LeEM4X6u4+qBl2OYZ8grigw=');

        $responsavel->fromArray($responsavel);
        $responsavel->fromArray($responsavel->toArray());
        $responsavel->fromArray(null);

        $xml = $responsavel->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/responsavel/testResponsavelXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/responsavel/testResponsavelXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testResponsavelLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/responsavel/testResponsavelXML.xml');

        $responsavel = new \NFe\Entity\Responsavel();
        $responsavel->loadNode($dom_cmp->documentElement);

        $xml = $responsavel->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testResponsavelLoadTagNameInvalidXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/responsavel/testResponsavelXML.xml');

        $responsavel = new \NFe\Entity\Responsavel();
        $this->expectException('\Exception');
        $responsavel->loadNode($dom_cmp->documentElement, 'respTec');
    }

    public function testResponsavelLoadElementXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/responsavel/testResponsavelInvalidXML.xml');

        $responsavel = new \NFe\Entity\Responsavel();
        $responsavel->loadNode($dom_cmp->documentElement, 'infRespTec');

        // Nó principal
        $test  = $dom_cmp->createElement('test');
        // Esse nó precisa ser filho do TEST
        $infRespTec = $dom_cmp->createElement('infRespTec');
        // Setando valores no nó infRespTec
        $infRespTec->appendChild($dom_cmp->createElement('CNPJ', $responsavel->getCNPJ()));
        $infRespTec->appendChild($dom_cmp->createElement('xContato', $responsavel->getContato()));
        $infRespTec->appendChild($dom_cmp->createElement('email', $responsavel->getEmail()));
        $infRespTec->appendChild($dom_cmp->createElement('fone', $responsavel->getTelefone()));
        $infRespTec->appendChild($dom_cmp->createElement('idCSRT', $responsavel->getIDCsrt()));
        $infRespTec->appendChild($dom_cmp->createElement('hashCSRT', $responsavel->getHashCsrt()));
        $test->appendChild($infRespTec);

        $dom = $test->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($test));
    }
}
