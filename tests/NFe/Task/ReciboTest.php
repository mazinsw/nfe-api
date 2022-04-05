<?php

namespace NFe\Task;

use NFe\Core\Nota;

class ReciboTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
    }

    public function autorizadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testReciboSOAP.xml',
            'task/testReciboAutorizadoReponseSOAP.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testReciboSOAP.xml',
            'task/testReciboRejeitadoReponseSOAP.xml'
        );
    }

    public function processamentoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testReciboSOAP.xml',
            'task/testReciboProcessamentoReponseSOAP.xml'
        );
    }

    public function testNormalization()
    {
        $recibo = new Recibo();
        $recibo->setModelo('65');
        $this->assertEquals(Nota::MODELO_NFCE, $recibo->getModelo());
        $this->assertEquals('65', $recibo->getModelo(true));
        $recibo->setModelo('55');
        $this->assertEquals(Nota::MODELO_NFE, $recibo->getModelo());
        $this->assertEquals('55', $recibo->getModelo(true));
        $recibo->setModelo('50');
        $this->assertEquals('50', $recibo->getModelo(true));
        $this->assertNull($recibo->getMensagem(true));
        $this->assertNull($recibo->getCodigo(true));
        $this->assertNull($recibo->getTempoMedio(true));
    }

    public function testReciboAutorizado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'autorizadoPostFunction']);
        try {
            $recibo = new Recibo();
            $recibo->setNumero('411000002567074');
            $retorno = $recibo->consulta($nota);
            $recibo->setMensagem('msg');
            $recibo->setCodigo('123');
            $recibo->setTempoMedio(3);
            $recibo->fromArray($recibo);
            $recibo->fromArray($recibo->toArray());
            $recibo->fromArray(null);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);

        if (getenv('TEST_MODE') == 'override') {
            $dom = $nota->addProtocolo($dom);
            $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAutorizadoXML.xml';
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testReciboRejeitado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        try {
            $recibo = new Recibo();
            $recibo->setNumero('411000002567074');
            $retorno = $recibo->consulta($nota);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $retorno);
        $this->assertEquals('785', $retorno->getStatus());
    }

    public function testReciboNaoProcessado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'processamentoPostFunction']);
        try {
            $recibo = new Recibo();
            $recibo->setNumero('411000002567074');
            $retorno = $recibo->consulta($nota);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Recibo', $retorno);
        $this->assertEquals('105', $retorno->getStatus());
    }

    public function testValidarEsquemaNotFound()
    {
        $recibo = new Recibo();
        $this->expectException('\Exception');
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->createElement('schema'));
        $recibo->validar($dom);
    }

    public function testReciboNaoValidado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $recibo = new Recibo();
        $recibo->setNumero(null); // não informa o número do rebibo
        // evita de enviar para a SEFAZ em caso de falha
        \NFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        try {
            $this->expectException('\\NFe\\Exception\\ValidationException');
            $recibo->consulta($nota);
        } catch (Exception $e) {
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testReciboLoadInvalidXML()
    {
        $recibo = new Recibo();
        $this->expectException('\Exception');
        $recibo->loadNode(new \DOMDocument(), Recibo::INFO_TAGNAME);
    }
}
