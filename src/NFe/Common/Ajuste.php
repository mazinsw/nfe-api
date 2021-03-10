<?php
/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
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
use NFe\Core\Nota;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;

/**
 * Configurações padrão para emissão de nota fiscal
 */
class Ajuste extends Configuracao implements Evento
{
    /**
     * Caminho onde será salvo os XMLs
     * @var string
     */
    private $pasta_xml_base;

    /**
     * Subpasta onde será salvo os XMLs das numerações inutilizadas
     * @var string
     */
    private $pasta_xml_inutilizado;

    /**
     * Subpasta onde será salvo os XMLs dos eventos de cancelamentos
     * @var string
     */
    private $pasta_xml_cancelado;

    /**
     * Subpasta onde será salvo os XMLs das notas pendentes de envio
     * @var string
     */
    private $pasta_xml_pendente;

    /**
     * Subpasta onde será salvo os XMLs das notas denegadas pela SEFAZ
     * @var string
     */
    private $pasta_xml_denegado;

    /**
     * Subpasta onde será salvo os XMLs das notas rejeitadas
     * @var string
     */
    private $pasta_xml_rejeitado;

    /**
     * Subpasta onde será salvo os XMLs das notas autorizadas
     * @var string
     */
    private $pasta_xml_autorizado;

    /**
     * Subpasta onde será salvo os XMLs das notas em processamento na SEFAZ
     * @var string
     */
    private $pasta_xml_processamento;

    /**
     * Subpasta onde será salvo os XMLs das notas assinadas com certificado digital
     * @var string
     */
    private $pasta_xml_assinado;

    /**
     * @param mixed $ajuste array ou intância
     */
    public function __construct($ajuste = [])
    {
        parent::__construct($ajuste);
        $this->setEvento($this);
        $this->setTempoLimite(30);
        $root_path = dirname(dirname(dirname(__DIR__)));
        $cert_dir = $root_path . '/storage/certs';
        $this->setArquivoChavePublica($cert_dir . '/public.pem');
        $this->setArquivoChavePrivada($cert_dir . '/private.pem');

        $this->setPastaXmlBase($root_path . '/storage/xml');
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
     * @return string
     */
    public function getPastaXmlBase()
    {
        return $this->pasta_xml_base;
    }

    /**
     * @param string $pasta_xml_base
     * @return self
     */
    public function setPastaXmlBase($pasta_xml_base)
    {
        $this->pasta_xml_base = $pasta_xml_base;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das inutilizações de números de notas
     * @param string $ambiente
     * @param string $caminho
     * @return string
     */
    protected function aplicaAmbiente($ambiente, $caminho)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $path = rtrim($this->getPastaXmlBase(), '/') . '/' . ltrim($caminho, '/');
        return rtrim(str_replace('{ambiente}', $ambiente, $path), '/');
    }

    /**
     * Exclui os arquivos XML desnecessários
     * @param \NFe\Core\Nota $nota nota
     */
    protected function deleteXmlAnteriores($nota)
    {
        $filename = $this->getPastaXmlRejeitado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlProcessamento($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlPendente($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * Pasta onde ficam os XML das inutilizações de números de notas
     * @param string $ambiente
     * @return string
     */
    public function getPastaXmlInutilizado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_inutilizado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_inutilizado);
    }

    /**
     * @param string $pasta_xml_inutilizado
     * @return self
     */
    public function setPastaXmlInutilizado($pasta_xml_inutilizado)
    {
        $this->pasta_xml_inutilizado = $pasta_xml_inutilizado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas após serem aceitas e depois canceladas
     * @param string $ambiente
     * @return string
     */
    public function getPastaXmlCancelado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_cancelado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_cancelado);
    }

    /**
     * @param string $pasta_xml_cancelado
     * @return self
     */
    public function setPastaXmlCancelado($pasta_xml_cancelado)
    {
        $this->pasta_xml_cancelado = $pasta_xml_cancelado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas pendentes de consulta
     * @param string $ambiente
     * @return string
     */
    public function getPastaXmlPendente($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_pendente;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_pendente);
    }

    /**
     * @param string $pasta_xml_pendente
     * @return self
     */
    public function setPastaXmlPendente($pasta_xml_pendente)
    {
        $this->pasta_xml_pendente = $pasta_xml_pendente;
        return $this;
    }

    /**
     * Pasta onde ficam os XMLs após enviados e denegados
     * @param string $ambiente
     * @return string
     */
    public function getPastaXmlDenegado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_denegado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_denegado);
    }

    /**
     * @param string $pasta_xml_denegado
     * @return self
     */
    public function setPastaXmlDenegado($pasta_xml_denegado)
    {
        $this->pasta_xml_denegado = $pasta_xml_denegado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas após serem enviadas e rejeitadas
     * @param string $ambiente
     * @return string
     */
    public function getPastaXmlRejeitado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_rejeitado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_rejeitado);
    }

    /**
     * @param string $pasta_xml_rejeitado
     * @return self
     */
    public function setPastaXmlRejeitado($pasta_xml_rejeitado)
    {
        $this->pasta_xml_rejeitado = $pasta_xml_rejeitado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas após serem enviados e aceitos pela
     * @param string $ambiente
     * @return string
     * SEFAZ
     */
    public function getPastaXmlAutorizado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_autorizado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_autorizado);
    }

    /**
     * @param string $pasta_xml_autorizado
     * @return self
     */
    public function setPastaXmlAutorizado($pasta_xml_autorizado)
    {
        $this->pasta_xml_autorizado = $pasta_xml_autorizado;
        return $this;
    }

    /**
     * Pasta onde ficam os XML das notas em processamento de retorno de
     * @param string $ambiente
     * @return string
     * autorização
     */
    public function getPastaXmlProcessamento($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_processamento;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_processamento);
    }

    /**
     * @param string $pasta_xml_processamento
     * @return self
     */
    public function setPastaXmlProcessamento($pasta_xml_processamento)
    {
        $this->pasta_xml_processamento = $pasta_xml_processamento;
        return $this;
    }

    /**
     * Pasta onde ficam os XMLs após assinado e antes de serem enviados
     * @param string $ambiente
     * @return string
     */
    public function getPastaXmlAssinado($ambiente = null)
    {
        if (is_null($ambiente)) {
            return $this->pasta_xml_assinado;
        }
        return $this->aplicaAmbiente($ambiente, $this->pasta_xml_assinado);
    }

    /**
     * @param string $pasta_xml_assinado
     * @return self
     */
    public function setPastaXmlAssinado($pasta_xml_assinado)
    {
        $this->pasta_xml_assinado = $pasta_xml_assinado;
        return $this;
    }

    /**
     * Chamado quando o XML da nota foi gerado,
     * aqui pode ser atualizado a chave da nota, data de emissão, além do estado da nota
     * O registro da nota pode ser encontrada pela chave ou pelo código
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaGerada($nota, $xml)
    {
    }

    /**
     * Chamado após o XML da nota ser assinado
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaAssinada($nota, $xml)
    {
    }

    /**
     * Chamado após o XML da nota ser validado com sucesso
     * Nesse ponto pode ser salvo o QR Code, URL de consulta, tributos e complementos da nota
     * além de atualizar o estado da nota para assinado
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaValidada($nota, $xml)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado antes de enviar a nota para a SEFAZ
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaEnviando($nota, $xml)
    {
    }

    /**
     * Chamado quando a forma de emissão da nota fiscal muda para contingência
     * Atualizar no banco flag de contingência informando a data de contingência e motivo
     * @param \NFe\Core\Nota $nota
     * @param bool $offline
     * @param \Exception $exception
     */
    public function onNotaContingencia($nota, $offline, $exception)
    {
        $this->deleteXmlAnteriores($nota);
    }

    /**
     * Chamado quando a nota foi enviada e aceita pela SEFAZ
     * Além de salvar o XML, a data de autorização, protocolo e estado devem ser salvos no banco
     * Nesse ponto essa nota deve ser marcada como concluída no banco de dados
     * Atenção: se a ação dessa nota for para cancelar, não troque a flag de conclusão
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaAutorizada($nota, $xml, $retorno)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlAutorizado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando a emissão da nota foi concluída com sucesso independente
     * da forma de emissão
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaCompleto($nota, $xml)
    {
    }

    /**
     * Chamado quando uma nota é rejeitada pela SEFAZ, a nota deve ser
     * corrigida para depois ser enviada novamente
     * Se a ação da nota for cancelar, deve-se mudar a ação para inutilizar se for rejeição de nota inexistente
     * Caso contrário, deve marcar a flag para corrigir a nota antes de enviar novamente
     * Além de marcar o estado como rejeitado
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaRejeitada($nota, $xml, $retorno)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlRejeitado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando a nota é denegada e não pode ser utilizada (outra nota
     * deve ser gerada)
     * Atualizar o estado da nota e marcar como concluída mas não corrigida
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaDenegada($nota, $xml, $retorno)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlDenegado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado após tentar enviar uma nota e não ter certeza se ela foi
     * recebida ou não (problemas técnicos), deverá ser feito uma consulta pela
     * chave para obter o estado da nota,
     * Aqui deve ser cancelada a nota incerta e gerar outra em contingência
     * Atenção: Nota em contingência pode ficar pendente também, nesse caso não se deve criar outra nota
     * Atualizar a nota incerta mudando a ação para cancelar e status para pendente
     * Criar outra nota baseado na nota incerta incrementando sua numeração para uma disponível
     * Atualizar o número da $nota e seu ID com a função gerarID, salvar também a chave na nova nota
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \Exception $exception
     */
    public function onNotaPendente($nota, $xml, $exception)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlPendente($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando uma nota é enviada, mas não retornou o protocolo que será
     * consultado mais tarde
     * Salvar o recibo na nota para consultar mais tarde além atualizar o status
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaProcessando($nota, $xml, $retorno)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlProcessamento($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando uma nota autorizada é cancelada na SEFAZ
     * Atualizar o protocolo do retorno, estado e flag de conclusão da nota
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaCancelada($nota, $xml, $retorno)
    {
        $this->deleteXmlAnteriores($nota);
        $filename = $this->getPastaXmlCancelado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }

    /**
     * Chamado quando ocorre um erro nas etapas de geração e envio da nota (Não
     * é chamado quando entra em contigência)
     * Alterar a flag para corrigir a nota se não for problema de rede
     * @param \NFe\Core\Nota $nota
     * @param \Exception $exception
     */
    public function onNotaErro($nota, $exception)
    {
    }

    /**
     * Chamado quando um ou mais números de notas forem inutilizados
     * @param \NFe\Task\Inutilizacao $inutilizacao
     * @param \DOMDocument $xml
     */
    public function onInutilizado($inutilizacao, $xml)
    {
        $filename = $this->getPastaXmlInutilizado($inutilizacao->getAmbiente()) . '/' . $inutilizacao->getID() . '.xml';
        Util::createDirectory(dirname($filename));
        file_put_contents($filename, $xml->saveXML());
    }
    
    /**
     * Chamado quando uma tarefa é executada com sucesso
     * @param \NFe\Task\Tarefa $tarefa
     * @param \NFe\Task\Retorno $retorno
     */
    public function onTarefaExecutada($tarefa, $retorno)
    {
        // não precisa implementar, pois a consulta já processa a nota internamente
        // se a intenção da consulta for para cancelar ou inutilizar
        // os eventos já estão preparados para manter a ação correta para posterior processamento
        // Pode acontecer de uma nota cancelada ser consultada
        $cancelamento = $tarefa->getAcao() == \NFe\Task\Tarefa::ACAO_CONSULTAR && $retorno->isCancelado();

        if ($tarefa->getAcao() == \NFe\Task\Tarefa::ACAO_INUTILIZAR) {
            // implementar aqui pois o evento de inutilização não devolve o ID da nota no banco
            // atualizar a chave, protocolo, data de autorização, flag de conclusão e estado da inutilização
        } elseif ($tarefa->getAcao() == \NFe\Task\Tarefa::ACAO_CANCELAR || $cancelamento) {
            // salva um XML diferenciado e não embutido no XML da nota
            $nota = $tarefa->getNota();
            $path = $this->getPastaXmlCancelado($nota->getAmbiente());
            $filename = $path . '/' . $nota->getID() . '-procEventoNFe.xml';
            $xml = $tarefa->getDocumento();
            Util::createDirectory(dirname($filename));
            file_put_contents($filename, $xml->saveXML());
        }
    }

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     * Altera a flag de correção da nota para evitar ficar enviando a nota a todo momento
     * Atenção: Não alterar a flag de correção se o erro da tarefa for causado por problema de rede
     * @param \NFe\Task\Tarefa $tarefa
     * @param \Exception $exception
     */
    public function onTarefaErro($tarefa, $exception)
    {
    }

    /**
     * @param bool $recursive
     * @return array
     */
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

    /**
     * @param mixed $ajuste array ou instância
     * @return self
     */
    public function fromArray($ajuste = [])
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
