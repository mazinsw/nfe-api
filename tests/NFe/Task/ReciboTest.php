<?php
namespace NFe\Task;

class ReciboTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
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

    public function testReciboAutorizado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'autorizadoPostFunction'));
        try {
            $recibo = new Recibo();
            $recibo->setNumero('411000002567074');
            $retorno = $recibo->consulta($nota);
            $recibo->fromArray($recibo);
            $recibo->fromArray(null);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());

        // $dom = $nota->addProtocolo($dom);
        // $xml_file = dirname(dirname(__DIR__)).'/resources/xml/nota/testNFCeAutorizadoXML.xml';
        // $dom->formatOutput = true;
        // file_put_contents($xml_file, $dom->saveXML());
    }

    public function testReciboRejeitado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'rejeitadoPostFunction'));
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
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'processamentoPostFunction'));
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

    public function testReciboNaoValidado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $recibo = new Recibo();
        $recibo->setNumero(null); // não informa o número do rebibo
        // evita de enviar para a SEFAZ em caso de falha
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'rejeitadoPostFunction'));
        try {
            $this->setExpectedException('\\NFe\\Exception\\ValidationException');
            $recibo->consulta($nota);
        } catch (Exception $e) {
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testReciboLoadInvalidXML()
    {
        $recibo = new Recibo();
        $this->setExpectedException('\Exception');
        $recibo->loadNode(new \DOMDocument(), Recibo::INFO_TAGNAME);
    }
}
