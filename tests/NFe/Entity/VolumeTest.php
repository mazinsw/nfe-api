<?php

namespace NFe\Entity;

class VolumeTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testVolumeXML()
    {
        $volume = new \NFe\Entity\Volume();
        $volume->setQuantidade(2);
        $volume->setEspecie('caixa');
        $volume->setMarca('MZSW');
        $volume->addNumeracao(1);
        $volume->addNumeracao(2);
        $volume->addNumeracao(3);
        $volume->getPeso()
            ->setLiquido(15.0)
            ->setBruto(21.0);
        $volume->addLacre(new \NFe\Entity\Lacre(['numero' => 123456]));
        $volume->addLacre(new \NFe\Entity\Lacre(['numero' => 123457]));
        $volume->addLacre(new \NFe\Entity\Lacre(['numero' => 123458]));
        $volume->fromArray($volume);
        $volume->fromArray($volume->toArray());
        $volume->fromArray(null);

        $xml = $volume->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/volume/testVolumeXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/volume/testVolumeXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testVolumeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/volume/testVolumeXML.xml');

        $volume = new \NFe\Entity\Volume();
        $volume->loadNode($dom_cmp->documentElement);

        $xml = $volume->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testVolumeSemPesoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(__DIR__)) . '/resources/xml/volume/testVolumeSemPesoXML.xml');

        $volume = new \NFe\Entity\Volume();
        $element = $volume->loadNode($dom_cmp->documentElement);

        $xml = $volume->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($element);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testVolumeInvalidLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->loadXML('<invalid/>');

        $volume = new \NFe\Entity\Volume();
        $this->expectException('\Exception');
        $element = $volume->loadNode($dom_cmp->documentElement);
    }
}
