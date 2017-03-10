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
namespace NFe\Common;

use NFe\Task\Tarefa;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;

/**
 * Configurações padrão para emissão de nota fiscal
 */
class Ajuste extends Configuracao implements Evento
{

    private $pasta_xml_base;
    private $pasta_xml_inutilizado;
    private $pasta_xml_cancelado;
    private $pasta_xml_pendente;
    private $pasta_xml_denegado;
    private $pasta_xml_rejeitado;
    private $pasta_xml_autorizado;
    private $pasta_xml_processamento;
    private $pasta_xml_assinado;

    public function __construct($ajuste = array())
    {
        parent::__construct($ajuste);
        $this->setEvento($this);
        //$this->setSincrono(true);
        $this->setTempoLimite(4);
        $cert_dir = dirname(dirname(dirname(__DIR__))) . '/docs/cert';
        $this->setArquivoChavePublica($cert_dir . '/public.pem');
        $this->setArquivoChavePrivada($cert_dir . '/private.pem');

        $this->setPastaXmlBase(dirname(dirname(dirname(__DIR__))) . '/site/xml');
        $this->setPastaXmlInutilizado('{ambiente}/inutilizado');
        $this->setPastaXmlCancelado('{ambiente}/cancelado');
        $this->setPastaXmlPendente('{ambiente}/pendente');
        $this->setPastaXmlDenegado('{ambiente}/denegado');
        $this->setPastaXmlRejeitado('{ambiente}/rejeitado');
        $this->setPastaXmlAutorizado('{ambiente}/autorizado');
        $this->setPastaXmlProcessamento('{ambiente}/processamento');
        $this->setPastaXmlAssinado('{ambiente}/assinado');
    }

    /**
     * Caminho da pasta base para armazenamento dos XML
     */
    public function getPastaXmlBase()
    {
        return $this->pasta_xml_base;
    }

    public function setPastaXmlBase($pasta_xml_base)
    {
        $this->pasta_xml_base = $pasta_xml_base;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das inutilizações de números de notas
     */
    private function aplicaAmbiente($ambiente, $caminho)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = self::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = self::AMBIENTE_HOMOLOGACAO;
                break;
        }
        return rtrim(str_replace('{ambiente}', $ambiente, rtrim($this->getPastaXmlBase(), '/').
            '/'.ltrim($caminho, '/')), '/');
    }

    /**
     * Pasta onde ficam os XML das inutilizações de números de notas
     */
    public function getPastaXmlInutilizado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_inutilizado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_inutilizado);
    }

    public function setPastaXmlInutilizado($pasta_xml_inutilizado)
    {
        $this->pasta_xml_inutilizado = $pasta_xml_inutilizado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas após serem aceitas e depois canceladas
     */
    public function getPastaXmlCancelado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_cancelado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_cancelado);
    }

    public function setPastaXmlCancelado($pasta_xml_cancelado)
    {
        $this->pasta_xml_cancelado = $pasta_xml_cancelado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas pendentes de consulta
     */
    public function getPastaXmlPendente($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_pendente;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_pendente);
    }

    public function setPastaXmlPendente($pasta_xml_pendente)
    {
        $this->pasta_xml_pendente = $pasta_xml_pendente;
        return $this;
    }

    /**
     * Pasta onde ficam os XMLs após enviados e denegados
     */
    public function getPastaXmlDenegado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_denegado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_denegado);
    }

    public function setPastaXmlDenegado($pasta_xml_denegado)
    {
        $this->pasta_xml_denegado = $pasta_xml_denegado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas após serem enviadas e rejeitadas
     */
    public function getPastaXmlRejeitado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_rejeitado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_rejeitado);
    }

    public function setPastaXmlRejeitado($pasta_xml_rejeitado)
    {
        $this->pasta_xml_rejeitado = $pasta_xml_rejeitado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas após serem enviados e aceitos pela
     * SEFAZ
     */
    public function getPastaXmlAutorizado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_autorizado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_autorizado);
    }

    public function setPastaXmlAutorizado($pasta_xml_autorizado)
    {
        $this->pasta_xml_autorizado = $pasta_xml_autorizado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas em processamento de retorno de
     * autorização
     */
    public function getPastaXmlProcessamento($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_processamento;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_processamento);
    }

    public function setPastaXmlProcessamento($pasta_xml_processamento)
    {
        $this->pasta_xml_processamento = $pasta_xml_processamento;
        return $this;
    }

    /**
     * Pasta onde ficam os XMLs após assinado e antes de serem enviados
     */
    public function getPastaXmlAssinado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_assinado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_assinado);
    }

    public function setPastaXmlAssinado($pasta_xml_assinado)
    {
        $this->pasta_xml_assinado = $pasta_xml_assinado;
        return $this;
    }

    /**
     * Chamado quando o XML da nota foi gerado
     */
    public function onNotaGerada($nota, $xml)
    {
        //echo 'XML gerado!<br>';
    }

    /**
     * Chamado após o XML da nota ser assinado
     */
    public function onNotaAssinada($nota, $xml)
    {
        //echo 'XML assinado!<br>';
    }

    /**
     * Chamado após o XML da nota ser validado com sucesso
     */
    public function onNotaValidada($nota, $xml)
    {
        //echo 'XML validado!<br>';
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
        $dom = new \DOMDocument();
        $dom->load($filename);
        $adapter = new XmlseclibsAdapter();
        if (!$adapter->verify($dom)) {
            throw new \Exception('Falha na assinatura do XML');
        }
    }

    /**
     * Chamado antes de enviar a nota para a SEFAZ
     */
    public function onNotaEnviando($nota, $xml)
    {
        //echo 'Enviando XML...<br>';
    }

    /**
     * Chamado quando a forma de emissão da nota fiscal muda para contigência,
     * aqui deve ser decidido se o número da nota deverá ser pulado e se esse
     * número deve ser cancelado ou inutilizado
     */
    public function onNotaContingencia($nota, $offline, $exception)
    {
        echo 'Forma de emissão alterada para "'.$nota->getEmissao().'" <br>';
        // remove o XML salvo anteriormente com a emissão normal
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename) && $offline) {
            unlink($filename); // só remove se tiver certeza que nenhum dado foi enviado para a SEFAZ
        }
        // incrementa o número da nota se existir a possibilidade de ter enviado com sucesso
        if (!$offline) {
            $nota->setNumero($nota->getNumero() + 1);
        }
    }

    /**
     * Chamado quando a nota foi enviada e aceita pela SEFAZ
     */
    public function onNotaAutorizada($nota, $xml, $retorno)
    {
        //echo 'XML autorizado com sucesso!<br>';

        // TODO: obter o estado da nota e remover apenas do local correto
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlProcessamento($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlAutorizado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando a emissão da nota foi concluída com sucesso independente
     * da forma de emissão
     */
    public function onNotaCompleto($nota, $xml)
    {
        //echo 'XML processado com sucesso!<br>';
    }

    /**
     * Chamado quando uma nota é rejeitada pela SEFAZ, a nota deve ser
     * corrigida para depois ser enviada novamente
     */
    public function onNotaRejeitada($nota, $xml, $retorno)
    {
        //echo 'XML rejeitado!<br>';

        // TODO: obter o estado da nota e remover apenas do local correto
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlProcessamento($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlRejeitado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando a nota é denegada e não pode ser utilizada (outra nota
     * deve ser gerada)
     */
    public function onNotaDenegada($nota, $xml, $retorno)
    {
        //echo 'XML denagado!<br>';

        // TODO: obter o estado da nota e remover apenas do local correto
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlProcessamento($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlDenegado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado após tentar enviar uma nota e não ter certeza se ela foi
     * recebida ou não (problemas técnicos), deverá ser feito uma consulta pela
     * chave para obter o estado da nota
     */
    public function onNotaPendente($nota, $xml, $exception)
    {
        //echo 'XML pendente!<br>';
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlPendente($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando uma nota é enviada, mas não retornou o protocolo que será
     * consultado mais tarde
     */
    public function onNotaProcessando($nota, $xml, $retorno)
    {
        //echo 'XML em processamento!<br>';
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlProcessamento($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando uma nota autorizada é cancelada na SEFAZ
     */
    public function onNotaCancelada($nota, $xml, $retorno)
    {
        //echo 'XML cancelado!<br>';
        $filename = $this->getPastaXmlAutorizado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }
        
        $filename = $this->getPastaXmlCancelado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando ocorre um erro nas etapas de geração e envio da nota (Não
     * é chamado quando entra em contigência)
     */
    public function onNotaErro($nota, $exception)
    {
        echo 'Falha no processamento da nota: '.$exception->getMessage().'<br>';
    }

    /**
     * Chamado quando um ou mais números de notas forem inutilizados
     */
    public function onInutilizado($inutilizacao, $xml)
    {
        $filename = $this->getPastaXmlInutilizado($inutilizacao->getAmbiente()) . '/' . $inutilizacao->getID() . '.xml';
        file_put_contents($filename, $xml->saveXML());
    }
    
    /**
     * Chamado quando uma ação é executada
     */
    public function onTarefaExecutada($tarefa, $retorno)
    {
        //echo 'Tarefa executada!<br>';
        $nota = $tarefa->getNota();
        $xml = $tarefa->getDocumento();
        if ($tarefa->getAcao() == Tarefa::ACAO_CANCELAR && $retorno->isCancelado()) {
            $filename = $this->getPastaXmlCancelado($nota->getAmbiente()) . '/' . $nota->getID() . '-procEventoNFe.xml';
            file_put_contents($filename, $xml->saveXML());
        }
    }

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     */
    public function onTarefaErro($tarefa, $exception)
    {
        echo 'Falha no processamento da tarefa: '.$exception->getMessage().'<br>';
    }

    public function toArray($recursive = false)
    {
        $ajuste = parent::toArray($recursive);
        $ajuste['pasta_xml_base'] = $this->getPastaXmlBase();
        $ajuste['pasta_xml_inutilizado'] = $this->getPastaXmlInutilizado();
        $ajuste['pasta_xml_cancelado'] = $this->getPastaXmlCancelado();
        $ajuste['pasta_xml_pendente'] = $this->getPastaXmlPendente();
        $ajuste['pasta_xml_denegado'] = $this->getPastaXmlDenegado();
        $ajuste['pasta_xml_rejeitado'] = $this->getPastaXmlRejeitado();
        $ajuste['pasta_xml_autorizado'] = $this->getPastaXmlAutorizado();
        $ajuste['pasta_xml_processamento'] = $this->getPastaXmlProcessamento();
        $ajuste['pasta_xml_assinado'] = $this->getPastaXmlAssinado();
        return $ajuste;
    }

    public function fromArray($ajuste = array())
    {
        if ($ajuste instanceof Ajuste) {
            $ajuste = $ajuste->toArray();
        } elseif (!is_array($ajuste)) {
            return $this;
        }
        parent::fromArray($ajuste);
        if (isset($ajuste['pasta_xml_base'])) {
            $this->setPastaXmlBase($ajuste['pasta_xml_base']);
        } else {
            $this->setPastaXmlBase(null);
        }
        if (isset($ajuste['pasta_xml_inutilizado'])) {
            $this->setPastaXmlInutilizado($ajuste['pasta_xml_inutilizado']);
        } else {
            $this->setPastaXmlInutilizado(null);
        }
        if (isset($ajuste['pasta_xml_cancelado'])) {
            $this->setPastaXmlCancelado($ajuste['pasta_xml_cancelado']);
        } else {
            $this->setPastaXmlCancelado(null);
        }
        if (isset($ajuste['pasta_xml_pendente'])) {
            $this->setPastaXmlPendente($ajuste['pasta_xml_pendente']);
        } else {
            $this->setPastaXmlPendente(null);
        }
        if (isset($ajuste['pasta_xml_denegado'])) {
            $this->setPastaXmlDenegado($ajuste['pasta_xml_denegado']);
        } else {
            $this->setPastaXmlDenegado(null);
        }
        if (isset($ajuste['pasta_xml_rejeitado'])) {
            $this->setPastaXmlRejeitado($ajuste['pasta_xml_rejeitado']);
        } else {
            $this->setPastaXmlRejeitado(null);
        }
        if (isset($ajuste['pasta_xml_autorizado'])) {
            $this->setPastaXmlAutorizado($ajuste['pasta_xml_autorizado']);
        } else {
            $this->setPastaXmlAutorizado(null);
        }
        if (isset($ajuste['pasta_xml_processamento'])) {
            $this->setPastaXmlProcessamento($ajuste['pasta_xml_processamento']);
        } else {
            $this->setPastaXmlProcessamento(null);
        }
        if (isset($ajuste['pasta_xml_assinado'])) {
            $this->setPastaXmlAssinado($ajuste['pasta_xml_assinado']);
        } else {
            $this->setPastaXmlAssinado(null);
        }
        return $this;
    }
}
