<?php
namespace NFe\Entity;

class TransporteTest extends \PHPUnit_Framework_TestCase
{
    private $resource_path;
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZ::getInstance(true);
        $this->resource_path = dirname(dirname(__DIR__)) . '/resources';
    }

    protected function createTransporte()
    {
        $transporte = new \NFe\Entity\Transporte();
        $transporte->setFrete(\NFe\Entity\Transporte::FRETE_EMITENTE);
        $transporte->getVeiculo()
                   ->setRNTC(123456789)
                   ->setPlaca('ALK1232')
                   ->setUF('PR');
        $transporte->getReboque()
                   ->setPlaca('KLM1234')
                   ->setUF('PI');
        $transporte->setVagao('2A');
        $transporte->setBalsa('522');

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

        $transportador->setEndereco($endereco);
        
        $transporte->setTransportador($transportador);

        $retencao = new \NFe\Entity\Transporte\Tributo();
        $retencao->setServico(300.00);
        $retencao->setBase(300.00);
        $retencao->setAliquota(12.00);
        $retencao->setCFOP('5351');
        $retencao->getMunicipio()
                 ->setNome('Paranavaí')
                 ->getEstado()
                 ->setUF('PR');

        $transporte->setRetencao($retencao);

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
        $volume->addLacre(new \NFe\Entity\Lacre(array('numero' => 123456)));
        $volume->addLacre(new \NFe\Entity\Lacre(array('numero' => 123457)));
        $volume->addLacre(new \NFe\Entity\Lacre(array('numero' => 123458)));

        $transporte->addVolume($volume);
        $transporte->fromArray($transporte);
        $transporte->fromArray($transporte->toArray());
        $transporte->fromArray(null);

        return $transporte;
    }

    public function testTransporteXML()
    {
        $transporte = $this->createTransporte();

        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/transporte/testTransporteXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/transporte/testTransporteXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testTransporteLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/transporte/testTransporteXML.xml');

        $transporte = new \NFe\Entity\Transporte();
        $transporte->loadNode($dom_cmp->documentElement);

        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransporteFreteDestinatarioXML()
    {
        $transporte = $this->createTransporte();
        $transporte->setFrete('3'); // Número não reconhecido no frete
        $transporte->setFrete($transporte->getFrete(true));
        $transporte->setFrete(\NFe\Entity\Transporte::FRETE_DESTINATARIO);
        
        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            $this->resource_path . '/xml/transporte/testTransporteFreteDestinatarioXML.xml'
        );
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/transporte/testTransporteFreteDestinatarioXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testTransporteFreteDestinatarioLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            $this->resource_path . '/xml/transporte/testTransporteFreteDestinatarioXML.xml'
        );

        $transporte = new \NFe\Entity\Transporte();
        $transporte->loadNode($dom_cmp->documentElement);

        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransporteFreteTerceirosXML()
    {
        $transporte = $this->createTransporte();
        $transporte->setFrete(\NFe\Entity\Transporte::FRETE_TERCEIROS);
        
        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            $this->resource_path . '/xml/transporte/testTransporteFreteTerceirosXML.xml'
        );
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/transporte/testTransporteFreteTerceirosXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testTransporteFreteTerceirosLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(
            $this->resource_path . '/xml/transporte/testTransporteFreteTerceirosXML.xml'
        );

        $transporte = new \NFe\Entity\Transporte();
        $transporte->loadNode($dom_cmp->documentElement);

        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransporteNenhumXML()
    {
        $transporte = new \NFe\Entity\Transporte();
        $transporte->setFrete(\NFe\Entity\Transporte::FRETE_NENHUM);

        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/transporte/testTransporteNenhumXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));

        // $dom->formatOutput = true;
        // file_put_contents(
        //     $this->resource_path . '/xml/transporte/testTransporteNenhumXML.xml',
        //     $dom->saveXML($xml)
        // );
    }

    public function testTransporteNenhumLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/transporte/testTransporteNenhumXML.xml');

        $transporte = new \NFe\Entity\Transporte();
        $transporte->loadNode($dom_cmp->documentElement);

        $xml = $transporte->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testTransporteSemModalidadeLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/transporte/testTransporteSemModalidadeXML.xml');

        $transporte = new \NFe\Entity\Transporte();
        $this->setExpectedException('\Exception');
        $transporte->loadNode($dom_cmp->documentElement);
    }

    public function testTransporteInvalidLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->loadXML('<invalid/>');

        $transporte = new \NFe\Entity\Transporte();
        $this->setExpectedException('\Exception');
        $transporte->loadNode($dom_cmp->documentElement);
    }
}
