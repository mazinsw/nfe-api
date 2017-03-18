<?php
/**
 * MIT License
 *
 * Copyright (c) 2016 MZ Desenvolvimento de Sistemas LTDA
 *
 * @author Francimar Alves <mazinsw@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */
namespace NFe\Core;

use NFe\Log\Logger;
use NFe\Task\Tarefa;
use NFe\Task\Autorizacao;
use NFe\Common\Ajuste;

/**
 * Classe que envia uma ou mais notas fiscais para os servidores da sefaz
 */
class SEFAZ
{

    private $notas;
    private $configuracao;
    private static $instance;

    public function __construct($sefaz = array())
    {
        $this->fromArray($sefaz);
    }

    public static function getInstance($new = false)
    {
        if (is_null(self::$instance) || $new) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getNotas()
    {
        return $this->notas;
    }

    public function setNotas($notas)
    {
        $this->notas = $notas;
        return $this;
    }

    public function addNota($nota)
    {
        $this->notas[] = $nota;
        return $this;
    }

    public function getConfiguracao()
    {
        return $this->configuracao;
    }

    public function setConfiguracao($configuracao)
    {
        $this->configuracao = $configuracao;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $sefaz = array();
        if ($recursive) {
            $notas = array();
            $_notas = $this->getNotas();
            foreach ($_notas as $_nota) {
                $notas[] = $_nota->toArray($recursive);
            }
            $sefaz['notas'] = $notas;
        } else {
            $sefaz['notas'] = $this->getNotas();
        }
        if (!is_null($this->getConfiguracao()) && $recursive) {
            $sefaz['configuracao'] = $this->getConfiguracao()->toArray($recursive);
        } else {
            $sefaz['configuracao'] = $this->getConfiguracao();
        }
        return $sefaz;
    }

    public function fromArray($sefaz = array())
    {
        if ($sefaz instanceof SEFAZ) {
            $sefaz = $sefaz->toArray();
        } elseif (!is_array($sefaz)) {
            return $this;
        }
        if (!isset($sefaz['notas']) || is_null($sefaz['notas'])) {
            $this->setNotas(array());
        } else {
            $this->setNotas($sefaz['notas']);
        }
        if (!isset($sefaz['configuracao']) || is_null($sefaz['configuracao'])) {
            $this->setConfiguracao(new Ajuste());
        } else {
            $this->setConfiguracao($sefaz['configuracao']);
        }
        return $this;
    }

    private function despacha($nota, &$dom, $retorno)
    {
        $evento = $this->getConfiguracao()->getEvento();
        if ($retorno->isRecebido()) {
            Logger::debug('SEFAZ.despacha - Recibo: '.$retorno->getNumero().' da '.$nota->getID(true));
            $evento->onNotaProcessando($nota, $dom, $retorno);
        } elseif ($retorno->isAutorizado()) {
            $dom = $nota->addProtocolo($dom);
            Logger::debug('SEFAZ.despacha('.$retorno->getStatus().') - '.$retorno->getMotivo().
                ', Protocolo: '.$retorno->getNumero().' - '.$nota->getID(true));
            $evento->onNotaAutorizada($nota, $dom, $retorno);
        } elseif ($retorno->isDenegada()) {
            $evento->onNotaDenegada($nota, $dom, $retorno);
        } elseif ($retorno->isCancelado()) {
            $evento->onNotaCancelada($nota, $dom, $retorno);
        } else {
            $evento->onNotaRejeitada($nota, $dom, $retorno);
            throw new \Exception($retorno->getMotivo(), $retorno->getStatus());
        }
    }

    /**
     * Envia as notas adicionadas para a SEFAZ, caso não consiga, torna-as em contingência
     * todos os status são informados no evento da configuração, caso não possua evento,
     * uma \Exception será lançada na primeira falha
     */
    public function autoriza()
    {
        $i = 0;
        $evento = $this->getConfiguracao()->getEvento();
        foreach ($this->getNotas() as $nota) {
            try {
                $envia = true;
                do {
                    $dom = $nota->getNode()->ownerDocument;
                    $evento->onNotaGerada($nota, $dom);
                    $dom = $nota->assinar($dom);
                    $evento->onNotaAssinada($nota, $dom);
                    $dom = $nota->validar($dom); // valida o XML da nota
                    $evento->onNotaValidada($nota, $dom);
                    if (!$envia) {
                        break;
                    }
                    $evento->onNotaEnviando($nota, $dom);
                    $autorizacao = new Autorizacao();
                    try {
                        $retorno = $autorizacao->envia($nota, $dom);
                    } catch (\Exception $e) {
                        $partial_response = $e instanceof \NFe\Exception\IncompleteRequestException;
                        if ($partial_response) {
                            $evento->onNotaPendente($nota, $dom, $e);
                        }
                        if ($nota->getEmissao() == Nota::EMISSAO_CONTINGENCIA) {
                            throw $e;
                        }
                        Logger::debug('SEFAZ.autoriza('.$e->getCode().') - Mudando emissão para contingência: '.
                            $e->getMessage().' - '.$nota->getID(true));
                        $msg = substr('Falha no envio da nota: '.$e->getMessage(), 0, 256);
                        $nota->setEmissao(Nota::EMISSAO_CONTINGENCIA);
                        $nota->setDataContingencia(time());
                        $nota->setJustificativa($msg);
                        $evento->onNotaContingencia($nota, !$partial_response, $e);
                        $envia = false;
                        continue;
                    }
                    Logger::debug('SEFAZ.autoriza('.$retorno->getStatus().') - '.
                        $retorno->getMotivo().' - '.$nota->getID(true));
                    $this->despacha($nota, $dom, $retorno);
                    break;
                } while (true);
                $evento->onNotaCompleto($nota, $dom);
                $i++;
            } catch (\Exception $e) {
                Logger::error('SEFAZ.autoriza('.$e->getCode().') - '.$e->getMessage());
                $evento->onNotaErro($nota, $e);
            }
        }
        return $i;
    }

    /**
     * Consulta o status das notas em processamento
     */
    public function consulta($pendencias)
    {
        $i = 0;
        $evento = $this->getConfiguracao()->getEvento();
        foreach ($pendencias as $pendencia) {
            $nota = $pendencia->getNota();
            try {
                $retorno = $pendencia->executa();
                $dom = $pendencia->getDocumento();
                Logger::debug('SEFAZ.consulta('.$retorno->getStatus().') - '.
                    $retorno->getMotivo().' - '.$nota->getID(true));
                $this->despacha($nota, $dom, $retorno);
                $evento->onNotaCompleto($nota, $dom);
                $pendencia->setDocumento($dom);
                $evento->onTarefaExecutada($pendencia, $retorno);
                $i++;
            } catch (\Exception $e) {
                Logger::error('SEFAZ.consulta('.$e->getCode().') - '.$e->getMessage());
                $evento->onNotaErro($nota, $e);
            }
        }
        return $i;
    }

    /* Consulta se as notas existem e cancela ou inutiliza seus números
	 * Também processa pedido de inutilização e cancelamento de notas 
	 */
    public function executa($tarefas)
    {
        $i = 0;
        $evento = $this->getConfiguracao()->getEvento();
        foreach ($tarefas as $tarefa) {
            try {
                $save_dom = $tarefa->getDocumento();
                $retorno = $tarefa->executa();
                $dom = $tarefa->getDocumento();
                Logger::debug('SEFAZ.executa('.$retorno->getStatus().') - '.$retorno->getMotivo().
                    ' - Tarefa: '.$tarefa->getID());
                switch ($tarefa->getAcao()) {
                    case Tarefa::ACAO_INUTILIZAR:
                        $inutilizacao = $tarefa->getAgente();
                        Logger::debug('SEFAZ.executa[inutiliza]('.$inutilizacao->getStatus().') - '.
                            $inutilizacao->getMotivo().' - '.$inutilizacao->getID(true));
                        $evento->onInutilizado($inutilizacao, $dom);
                        break;
                    default:
                        $nota = $tarefa->getNota();
                        $this->despacha($nota, $save_dom, $retorno);
                        $evento->onNotaCompleto($nota, $save_dom);
                }
                $evento->onTarefaExecutada($tarefa, $retorno);
                $i++;
            } catch (\Exception $e) {
                Logger::error('SEFAZ.executa('.$e->getCode().') - '.$e->getMessage());
                $evento->onTarefaErro($tarefa, $e);
            }
        }
        return $i;
    }

    /* *
	 * Inutiliza um intervalo de números de notas fiscais e insere o resultado no
	 * próprio objeto de inutilização
	 */
    public function inutiliza($inutilizacao)
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setAgente($inutilizacao);
        try {
            $this->executa(array($tarefa));
        } catch (\Exception $e) {
            Logger::error('SEFAZ.inutiliza('.$e->getCode().') - '.$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Obtém as notas pendentes de autorização e envia para a SEFAZ
     */
    public function processa()
    {
        $i = 0;
        /* Envia as notas não enviadas, rejeitadas e em contingência */
        try {
            $db = $this->getConfiguracao()->getBanco();
            $notas = $db->getNotasAbertas();
            $this->setNotas($notas);
            $i += $this->autoriza();
        } catch (\Exception $e) {
            Logger::error('SEFAZ.processa[autoriza]('.$e->getCode().') - '.$e->getMessage());
        }
        /* Consulta o status das notas em processamento */
        try {
            $db = $this->getConfiguracao()->getBanco();
            $pendencias = $db->getNotasPendentes();
            $i += $this->consulta($pendencias);
        } catch (\Exception $e) {
            Logger::error('SEFAZ.processa[pendentes]('.$e->getCode().') - '.$e->getMessage());
        }
        /* Consulta se as notas existem e cancela ou inutiliza seus números
		 * Também processa pedido de inutilização e cancelamento de notas 
		 */
        try {
            $db = $this->getConfiguracao()->getBanco();
            $tarefas = $db->getNotasTarefas();
            $i += $this->executa($tarefas);
        } catch (\Exception $e) {
            Logger::error('SEFAZ.processa[tarefas]('.$e->getCode().') - '.$e->getMessage());
        }
        return $i;
    }
}
