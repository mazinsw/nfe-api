<?php
namespace NFe\Task;

class TarefaTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
    }

    public function inutilizadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testInutilizaSOAP.xml',
            'task/testInutilizaInutilizadoReponseSOAP.xml'
        );
    }

    public function situacaoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoAutorizadoReponseSOAP.xml'
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

    public function reciboPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testReciboSOAP.xml',
            'task/testReciboAutorizadoReponseSOAP.xml'
        );
    }

    public function cancelarPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRegistradoReponseSOAP.xml'
        );
    }

    public function testTarefaInutilizacao()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setJustificativa('TESTE DO SISTEMA');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setNota($nota);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'inutilizadoPostFunction'));
        try {
            $retorno = $tarefa->executa();
            $tarefa->fromArray($tarefa);
            $tarefa->fromArray(null);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $inutilizacao = $tarefa->getAgente();
        $this->assertEquals('102', $inutilizacao->getStatus());
        $this->assertEquals('141170000156683', $inutilizacao->getNumero());

        $xml_file = dirname(dirname(__DIR__)).'/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = $tarefa->getDocumento();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());

        // $dom->formatOutput = true;
        // file_put_contents($xml_file, $dom->saveXML());
    }

    public function testTarefaInutilizacaoSemNota()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'inutilizadoPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testTarefaInutilizacaoInvalida()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setAgente(new Recibo());

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'inutilizadoPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testTarefaConsultaSituacao()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setNota($nota);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'situacaoPostFunction'));
        try {
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $tarefa->toArray(true);
        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testTarefaConsultaSituacaoCancelado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setNota($nota);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'canceladoPostFunction'));
        try {
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertTrue($retorno->isCancelado());
        $dom = $tarefa->getDocumento();

        $dom_cmp = EventoTest::loadEventoRegistradoXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());

        // $dom->formatOutput = true;
        // file_put_contents(
        //     dirname(dirname(__DIR__)).'/resources/xml/task/testEventoRegistrado.xml',
        //     $dom->saveXML()
        // );
    }

    public function testTarefaConsultaRecibo()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $recibo = new Recibo();
        $recibo->setNumero('411000002567074');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setAgente($recibo);
        $tarefa->setNota($nota);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'reciboPostFunction'));
        try {
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testTarefaConsultaSemNota()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'situacaoPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testTarefaConsultaInvalida()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setAgente(new Inutilizacao());

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'situacaoPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testTarefaCancelar()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeAutorizada();
        $nota = $data['nota'];
        $nota->setJustificativa('CANCELAMENTO DE PEDIDO');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);
        $tarefa->setNota($nota);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'cancelarPostFunction'));
        try {
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());


        $xml_file = dirname(dirname(__DIR__)).'/resources/xml/task/testEventoRegistrado.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = $tarefa->getDocumento();

        // dhRegEvento auto gerado, copia para testar
        $node_cmp = \NFe\Common\Util::findNode($dom_cmp, 'dhEvento');
        $node = \NFe\Common\Util::findNode($dom, 'dhEvento');
        $node_cmp->nodeValue = $node->nodeValue;
        // quando a data do evento muda, a assinatura muda também
        $node_cmp = \NFe\Common\Util::findNode($dom_cmp, 'DigestValue');
        $node = \NFe\Common\Util::findNode($dom, 'DigestValue');
        $node_cmp->nodeValue = $node->nodeValue;
        // quando a data do evento muda, a assinatura muda também
        $node_cmp = \NFe\Common\Util::findNode($dom_cmp, 'SignatureValue');
        $node = \NFe\Common\Util::findNode($dom, 'SignatureValue');
        $node_cmp->nodeValue = $node->nodeValue;

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
        
        // $dom->formatOutput = true;
        // file_put_contents($xml_file, $dom->saveXML());
    }

    public function testTarefaCancelarSemNota()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'cancelarPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testTarefaCancelarNotaNaoAutorizada()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setJustificativa('CANCELAMENTO DE PEDIDO');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);
        $tarefa->setNota($nota);

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'cancelarPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testTarefaCancelarInvalido()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);
        $tarefa->setAgente(new Recibo());

        \NFe\Common\CurlSoap::setPostFunction(array($this, 'cancelarPostFunction'));
        try {
            $this->setExpectedException('\Exception');
            $retorno = $tarefa->executa();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }
}
