<?php

namespace NFe\Entity\Transporte;

class TributoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
    }

    public function testTributoXML()
    {
        $retencao = new \NFe\Entity\Transporte\Tributo();
        $retencao->setServico(300.00);
        $retencao->setBase(300.00);
        $retencao->setAliquota(12.00);
        $retencao->setCFOP('5351');
        $retencao->getMunicipio()
                 ->setNome('Paranavaí')
                 ->getEstado()
                 ->setUF('PR');
        $retencao->fromArray($retencao);
        $retencao->fromArray($retencao->toArray());
        $retencao->fromArray(null);

        $xml = $retencao->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(dirname(__DIR__))) . '/resources/xml/transporte/testTributoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(dirname(__DIR__))) . '/resources/xml/transporte/testTributoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTributoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(dirname(__DIR__))) . '/resources/xml/transporte/testTributoXML.xml');

        $retencao = new \NFe\Entity\Transporte\Tributo();
        $retencao->loadNode($dom_cmp->documentElement);

        $xml = $retencao->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
