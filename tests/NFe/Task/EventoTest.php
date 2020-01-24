<?php
namespace NFe\Task;

use NFe\Core\Nota;

class EventoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function createEvento($nota)
    {
        $evento = new Evento();
        $evento->setData(strtotime('2017-03-18T16:12:12+00:00'));
        $evento->setOrgao(
            $nota->getEmitente()->getEndereco()->getMunicipio()->getEstado()->getUF()
        );
        $evento->setJustificativa('CANCELAMENTO DE PEDIDO');
        $evento->setAmbiente($nota->getAmbiente());
        $evento->setModelo($nota->getModelo());
        $evento->setIdentificador($nota->getEmitente()->getCNPJ());
        $evento->setNumero('141170000157685');
        $evento->setChave($nota->getID());
        return $evento;
    }

    public static function loadEventoRegistradoXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testEventoRegistrado.xml';
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->load($xml_file);
        return $dom;
    }

    public function registradoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRegistradoReponseSOAP.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRejeitadoReponseSOAP.xml'
        );
    }

    public function testEventoRegistrado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'registradoPostFunction']);
        try {
            $evento = self::createEvento($nota);
            $dom = $evento->getNode()->ownerDocument;
            $dom = $evento->assinar($dom);
            $retorno = $evento->envia($dom);
            $evento->fromArray($evento);
            $evento->fromArray(null);
            $evento->fromArray($evento->toArray());
            $dom = $evento->addInformacao($dom);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
        
        if (getenv('TEST_MODE') == 'external') {
            $dom->formatOutput = true;
            $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testEventoRegistrado.xml';
            file_put_contents($xml_file, $dom->saveXML());
        }

        $dom_cmp = self::loadEventoRegistradoXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testEventoRejeitado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        try {
            $evento = self::createEvento($nota);
            $dom = $evento->getNode()->ownerDocument;
            $dom = $evento->assinar($dom);
            $dom = $evento->validar($dom);
            $retorno = $evento->envia($dom);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Evento', $retorno);
        $this->assertEquals('573', $retorno->getStatus());
    }

    public function testNormalization()
    {
        $evento = new Evento();
        $evento->setEmail('email@email.com');
        $evento->setModelo('65');
        $this->assertEquals(Nota::MODELO_NFCE, $evento->getModelo());
        $this->assertEquals('65', $evento->getModelo(true));
        $evento->setModelo('55');
        $this->assertEquals(Nota::MODELO_NFE, $evento->getModelo());
        $this->assertEquals('55', $evento->getModelo(true));
        $evento->setModelo('50');
        $this->assertEquals('50', $evento->getModelo(true));
        $evento->fromArray($evento);
        $this->assertEquals('email@email.com', $evento->getEmail(true));
    }

    public function testEventoInvalido()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $evento = self::createEvento($nota);
        $evento->setAmbiente('Produção');
        $dom = $evento->assinar();
        $this->expectException('\Exception');
        $dom = $evento->validar($dom);
    }

    public function testEventoSemInformacao()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $evento = self::createEvento($nota);
        $this->expectException('\Exception');
        $dom = $evento->addInformacao(new \DOMDocument());
    }
}
