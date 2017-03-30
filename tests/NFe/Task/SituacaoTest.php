<?php
namespace NFe\Task;

class SituacaoTest extends \PHPUnit_Framework_TestCase
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
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoAutorizadoReponseSOAP.xml'
        );
    }

    public function inexistentePostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoInexistenteReponseSOAP.xml'
        );
    }

    public function canceladoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoCanceladoReponseSOAP.xml'
        );
    }

    public function testSituacaoAutorizado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'autorizadoPostFunction'));
        try {
            $situacao = new Situacao();
            $retorno = $situacao->consulta($nota);
            $situacao->fromArray($situacao);
            $situacao->fromArray(null);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testSituacaoInexistente()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'inexistentePostFunction'));
        try {
            $situacao = new Situacao();
            $retorno = $situacao->consulta($nota);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Situacao', $retorno);
        $this->assertEquals('785', $retorno->getStatus());
    }

    public function testSituacaoCancelado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'canceladoPostFunction'));
        try {
            $situacao = new Situacao();
            $retorno = $situacao->consulta($nota);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertTrue($situacao->isCancelado());
        $this->assertInstanceOf('\\NFe\\Task\\Evento', $retorno);
        // TODO: carregar assinatura do XML para evitar usar outro certificado
        $dom = $retorno->assinar();
        $dom = $retorno->validar($dom);
        // $dom = $retorno->getNode()->ownerDocument; // descomentar essa linha quando implementar
        // TODO: Fim do problema de assinatura
        $dom = $retorno->addInformacao($dom);
        $dom_cmp = EventoTest::loadEventoRegistradoXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());

        // $dom->formatOutput = true;
        // file_put_contents(
        //     dirname(dirname(__DIR__)).'/resources/xml/task/testEventoRegistrado.xml',
        //     $dom->saveXML()
        // );
    }

    public function testSituacaoInvalida()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setID('1');
        $situacao = new Situacao();
        $situacao->setModelo('Invalido');
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'inexistentePostFunction'));
        $this->setExpectedException('\Exception');
        try {
            $retorno = $situacao->consulta($nota);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }
}
