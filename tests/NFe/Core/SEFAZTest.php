<?php
namespace NFe\Core;

use NFe\Logger\Log;
use NFe\Task\Inutilizacao;
use NFe\Task\Tarefa;

class SEFAZTest extends \PHPUnit_Framework_TestCase implements \NFe\Common\Evento
{
    protected function setUp()
    {
        Log::getInstance()->setHandler(new \Monolog\Handler\NullHandler());
    }

    protected function tearDown()
    {
        Log::getInstance()->setHandler(null);
    }
    
    /**
     * @return \NFe\Core\SEFAZ default instance
     */
    public static function createSEFAZ()
    {
        $emitente = \NFe\Entity\EmitenteTest::createEmitente();
        $sefaz = SEFAZ::getInstance(true);
        $sefaz->getConfiguracao()
            ->setArquivoChavePublica(dirname(dirname(__DIR__)) . '/resources/certs/public.pem')
            ->setArquivoChavePrivada(dirname(dirname(__DIR__)) . '/resources/certs/private.pem')
            ->setEmitente($emitente);
        return $sefaz;
    }

    public function testInstancia()
    {
        $sefaz = SEFAZ::getInstance();
        $this->assertNotNull($sefaz);
        $this->assertNotNull($sefaz->getConfiguracao());
    }

    public function testNotas()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->addNota(new NFCe());
        $sefaz->addNota(new NFCe());
        $sefaz->fromArray($sefaz);
        $sefaz->fromArray($sefaz->toArray());
        $sefaz->fromArray(null);
        $this->assertCount(2, $sefaz->getNotas());
    }

    public function autorizadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Task\AutorizacaoTest::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaAutorizadoReponseSOAP.xml'
        );
    }

    public function testAutoriza()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $nota = \NFe\Core\NFCeTest::createNFCe($sefaz);
        \NFe\Common\CurlSoap::setPostFunction([$this, 'autorizadoPostFunction']);
        try {
            $sefaz->setNotas([]);
            $sefaz->addNota($nota);
            $this->assertEquals(1, $sefaz->autoriza());
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function networkErrorPostFunction($soap_curl, $url, $data)
    {
        throw new \NFe\Exception\IncompleteRequestException('Suposta falha parcial de rede', 500);
    }

    public function testAutorizaContingencia()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $nota = \NFe\Core\NFCeTest::createNFCe($sefaz);
        \NFe\Common\CurlSoap::setPostFunction([$this, 'networkErrorPostFunction']);
        try {
            $sefaz->setNotas([]);
            $sefaz->addNota($nota);
            $this->assertEquals(1, $sefaz->autoriza());
        } catch (Exception $e) {
            $sefaz->getConfiguracao()->setOffline(null);
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        $sefaz->getConfiguracao()->setOffline(null);
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testConsulta()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $this->assertEquals(0, $sefaz->consulta([]));
    }

    public function testExecuta()
    {
        $sefaz = self::createSEFAZ();
        $this->assertEquals(0, $sefaz->executa([]));
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

    public function testInutiliza()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $inutilizacao = \NFe\Task\InutilizacaoTest::criaInutilizacao();
        \NFe\Common\CurlSoap::setPostFunction([$this, 'inutilizadoPostFunction']);
        try {
            $this->assertTrue($sefaz->inutiliza($inutilizacao));
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testProcessa()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $this->assertEquals(0, $sefaz->processa());
    }

    public function testInutilizaFail()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $inutilizacao = new Inutilizacao();
        $this->assertFalse($sefaz->inutiliza($inutilizacao));
    }


    /**
     * Chamado quando o XML da nota foi gerado
     */
    public function onNotaGerada($nota, $xml)
    {
        // TODO: implement
    }

    /**
     * Chamado após o XML da nota ser assinado
     */
    public function onNotaAssinada($nota, $xml)
    {
        // TODO: implement
    }

    /**
     * Chamado após o XML da nota ser validado com sucesso
     */
    public function onNotaValidada($nota, $xml)
    {
        // TODO: implement
    }

    /**
     * Chamado antes de enviar a nota para a SEFAZ
     */
    public function onNotaEnviando($nota, $xml)
    {
        // TODO: implement
    }

    /**
     * Chamado quando a forma de emissão da nota fiscal muda para contigência,
     * aqui deve ser decidido se o número da nota deverá ser pulado e se esse
     * número deve ser cancelado ou inutilizado
     */
    public function onNotaContingencia($nota, $offline, $exception)
    {
        // TODO: implement
    }

    /**
     * Chamado quando a nota foi enviada e aceita pela SEFAZ
     */
    public function onNotaAutorizada($nota, $xml, $retorno)
    {
        // TODO: implement
    }

    /**
     * Chamado quando a emissão da nota foi concluída com sucesso independente
     * da forma de emissão
     */
    public function onNotaCompleto($nota, $xml)
    {
        // TODO: implement
    }

    /**
     * Chamado quando uma nota é rejeitada pela SEFAZ, a nota deve ser
     * corrigida para depois ser enviada novamente
     */
    public function onNotaRejeitada($nota, $xml, $retorno)
    {
        // TODO: implement
    }

    /**
     * Chamado quando a nota é denegada e não pode ser utilizada (outra nota
     * deve ser gerada)
     */
    public function onNotaDenegada($nota, $xml, $retorno)
    {
        // TODO: implement
    }

    /**
     * Chamado após tentar enviar uma nota e não ter certeza se ela foi
     * recebida ou não (problemas técnicos), deverá ser feito uma consulta pela
     * chave para obter o estado da nota
     */
    public function onNotaPendente($nota, $xml, $exception)
    {
        // TODO: implement
    }

    /**
     * Chamado quando uma nota é enviada, mas não retornou o protocolo que será
     * consultado mais tarde
     */
    public function onNotaProcessando($nota, $xml, $retorno)
    {
        // TODO: implement
    }

    /**
     * Chamado quando uma nota autorizada é cancelada na SEFAZ
     */
    public function onNotaCancelada($nota, $xml, $retorno)
    {
        // TODO: implement
    }

    /**
     * Chamado quando ocorre um erro nas etapas de geração e envio da nota
     */
    public function onNotaErro($nota, $exception)
    {
        // TODO: implement
    }

    /**
     * Chamado quando um ou mais números de notas forem inutilizados
     */
    public function onInutilizado($inutilizacao, $xml)
    {
        $xml_file = dirname(dirname(__DIR__)).'/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $xml->saveXML());
    }

    /**
     * Chamado quando uma tarefa é executada
     */
    public function onTarefaExecutada($tarefa, $retorno)
    {
        if ($tarefa->getAcao() == Tarefa::ACAO_INUTILIZAR) {
            $xml = $tarefa->getDocumento();
            $xml_file = dirname(dirname(__DIR__)).'/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
            $dom_cmp = new \DOMDocument();
            $dom_cmp->preserveWhiteSpace = false;
            $dom_cmp->load($xml_file);
            $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $xml->saveXML());
        }
    }

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     */
    public function onTarefaErro($tarefa, $exception)
    {
        throw $exception;
    }
}
