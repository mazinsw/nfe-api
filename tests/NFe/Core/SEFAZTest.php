<?php
namespace NFe\Core;

class SEFAZTest extends \PHPUnit_Framework_TestCase implements \NFe\Common\Evento
{
    protected function setUp()
    {
        \NFe\Log\Logger::getInstance()->setWriteFunction(
            function ($type, $message) {
            }
        );
    }

    protected function tearDown()
    {
        \NFe\Log\Logger::getInstance()->setWriteFunction(null);
    }
    
    public static function createSEFAZ()
    {
        $emitente = \NFe\Entity\EmitenteTest::createEmitente();
        $sefaz = \NFe\Core\SEFAZ::getInstance(true);
        $sefaz->getConfiguracao()
            ->setArquivoChavePublica(dirname(dirname(__DIR__)) . '/resources/certs/public.pem')
            ->setArquivoChavePrivada(dirname(dirname(__DIR__)) . '/resources/certs/private.pem')
            ->setEmitente($emitente);
        return $sefaz;
    }

    public function testInstancia()
    {
        $sefaz = \NFe\Core\SEFAZ::getInstance();
        $this->assertNotNull($sefaz);
        $this->assertNotNull($sefaz->getConfiguracao());
    }

    public function testNotas()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $sefaz->addNota(new \NFe\Core\NFCe());
        $sefaz->addNota(new \NFe\Core\NFCe());
        $sefaz->fromArray($sefaz);
        $sefaz->fromArray($sefaz->toArray());
        $sefaz->fromArray(null);
        $this->assertCount(2, $sefaz->getNotas());
    }

    public function testAutoriza()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $sefaz->setNotas(array());
        $this->assertEquals(0, $sefaz->autoriza());
    }

    public function testConsulta()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $this->assertEquals(0, $sefaz->consulta(array()));
    }

    public function testExecuta()
    {
        $sefaz = new \NFe\Core\SEFAZ();
        $this->assertEquals(0, $sefaz->executa(array()));
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
        // TODO: implement
    }

    /**
     * Chamado quando uma tarefa é executada
     */
    public function onTarefaExecutada($tarefa, $retorno)
    {
        // TODO: implement
    }

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     */
    public function onTarefaErro($tarefa, $exception)
    {
        // TODO: implement
    }
}
