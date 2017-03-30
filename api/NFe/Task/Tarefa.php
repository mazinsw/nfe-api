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
namespace NFe\Task;

class Tarefa
{

    /**
     * Ação a ser realizada sobre o objeto ou recibo
     */
    const ACAO_CONSULTAR = 'consultar';
    const ACAO_INUTILIZAR = 'inutilizar';
    const ACAO_CANCELAR = 'cancelar';

    private $id;
    private $acao;
    private $nota;
    private $documento;
    private $agente;
    private $resposta;

    public function __construct($tarefa = array())
    {
        $this->fromArray($tarefa);
    }

    /**
     * Código aleatório e opcional que identifica a tarefa
     */
    public function getID()
    {
        return $this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Ação a ser realizada sobre o objeto ou recibo
     */
    public function getAcao()
    {
        return $this->acao;
    }

    public function setAcao($acao)
    {
        $this->acao = $acao;
        return $this;
    }

    /**
     * Nota que será processada se informado
     */
    public function getNota()
    {
        return $this->nota;
    }

    public function setNota($nota)
    {
        $this->nota = $nota;
        return $this;
    }

    /**
     * Informa o XML do objeto, quando não informado o XML é gerado a partir do
     * objeto
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
        return $this;
    }

    /**
     * Agente que obteve ou vai obter a resposta, podendo ser: pedido de
     * inutilização(NF\Inutilizacao), recibo(NF\Recibo) ou pedido de
     * cancelamento(NF\Evento)
     */
    public function getAgente()
    {
        return $this->agente;
    }

    public function setAgente($agente)
    {
        $this->agente = $agente;
        return $this;
    }

    /**
     * Resposta da tarefa após ser executada
     */
    public function getResposta()
    {
        return $this->resposta;
    }

    public function setResposta($resposta)
    {
        $this->resposta = $resposta;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $tarefa = array();
        $tarefa['id'] = $this->getID();
        $tarefa['acao'] = $this->getAcao();
        if (!is_null($this->getNota()) && $recursive) {
            $tarefa['nota'] = $this->getNota()->toArray($recursive);
        } else {
            $tarefa['nota'] = $this->getNota();
        }
        if (!is_null($this->getDocumento()) && $recursive) {
            $tarefa['documento'] = $this->getDocumento()->saveXML();
        } else {
            $tarefa['documento'] = $this->getDocumento();
        }
        if (!is_null($this->getAgente()) && $recursive) {
            $tarefa['agente'] = $this->getAgente()->toArray($recursive);
        } else {
            $tarefa['agente'] = $this->getAgente();
        }
        if (!is_null($this->getResposta()) && $recursive) {
            $tarefa['resposta'] = $this->getResposta()->toArray($recursive);
        } else {
            $tarefa['resposta'] = $this->getResposta();
        }
        return $tarefa;
    }

    public function fromArray($tarefa = array())
    {
        if ($tarefa instanceof Tarefa) {
            $tarefa = $tarefa->toArray();
        } elseif (!is_array($tarefa)) {
            return $this;
        }
        if (isset($tarefa['id'])) {
            $this->setID($tarefa['id']);
        } else {
            $this->setID(null);
        }
        if (isset($tarefa['acao'])) {
            $this->setAcao($tarefa['acao']);
        } else {
            $this->setAcao(null);
        }
        if (isset($tarefa['nota'])) {
            $this->setNota($tarefa['nota']);
        } else {
            $this->setNota(null);
        }
        if (isset($tarefa['documento'])) {
            $this->setDocumento($tarefa['documento']);
        } else {
            $this->setDocumento(null);
        }
        if (isset($tarefa['agente'])) {
            $this->setAgente($tarefa['agente']);
        } else {
            $this->setAgente(null);
        }
        if (isset($tarefa['resposta'])) {
            $this->setResposta($tarefa['resposta']);
        } else {
            $this->setResposta(null);
        }
        return $this;
    }

    /**
     * Resposta da tarefa após ser executada
     */
    public function executa()
    {
        if (is_null($this->getID())) {
            $this->setID(Status::genLote());
        }
        $retorno = null;
        switch ($this->getAcao()) {
            case self::ACAO_CANCELAR:
                $retorno = $this->cancela();
                break;
            case self::ACAO_INUTILIZAR:
                $retorno = $this->inutiliza();
                break;
            case self::ACAO_CONSULTAR:
                $retorno = $this->consulta();
                break;
        }
        $this->setResposta($retorno);
        return $this->getResposta();
    }

    private function cancela()
    {
        $nota = $this->getNota();
        $evento = $this->getAgente();
        if (is_null($evento)) {
            if (is_null($nota)) {
                throw new \Exception('A nota não foi informada na tarefa de cancelamento "'.$this->getID().'"', 404);
            }
            if (is_null($nota->getProtocolo())) {
                throw new \Exception('A nota não possui protocolo de autorização para o cancelamento "'.
                    $this->getID().'"', 404);
            }
            $evento = new Evento();
            $evento->setData(time());
            $evento->setOrgao($nota->getEmitente()->getEndereco()->
                getMunicipio()->getEstado()->getUF());
            $evento->setJustificativa($nota->getJustificativa());
            $this->setAgente($evento);
        } elseif (!($evento instanceof Evento)) {
            throw new \Exception('O agente informado não é um evento', 500);
        }
        if (!is_null($nota)) {
            $evento->setAmbiente($nota->getAmbiente());
            $evento->setModelo($nota->getModelo());
            $evento->setIdentificador($nota->getEmitente()->getCNPJ());
            if (!is_null($nota->getProtocolo())) {
                $evento->setNumero($nota->getProtocolo()->getNumero());
            }
            $evento->setChave($nota->getID());
        }
        $dom = $evento->getNode()->ownerDocument;
        $dom = $evento->assinar($dom);
        $dom = $evento->validar($dom);
        $retorno = $evento->envia($dom);
        if ($retorno->isCancelado()) {
            $dom = $evento->addInformacao($dom);
            $this->setDocumento($dom);
        }
        return $retorno;
    }
    
    private function inutiliza()
    {
        $nota = $this->getNota();
        $inutilizacao = $this->getAgente();
        if (is_null($inutilizacao)) {
            if (is_null($nota)) {
                throw new \Exception('A nota não foi informada na tarefa de inutilização "'.$this->getID().'"', 404);
            }
            $inutilizacao = new Inutilizacao();
            $inutilizacao->setAno(date('Y'));
            $inutilizacao->setJustificativa($nota->getJustificativa());
            $this->setAgente($inutilizacao);
        } elseif (!($inutilizacao instanceof Inutilizacao)) {
            throw new \Exception('O agente informado não é uma inutilização', 500);
        }
        if (!is_null($nota)) {
            $inutilizacao->setCNPJ($nota->getEmitente()->getCNPJ());
            $inutilizacao->setSerie($nota->getSerie());
            $inutilizacao->setInicio($nota->getNumero());
            $inutilizacao->setFinal($nota->getNumero());
            $inutilizacao->setUF($nota->getEmitente()->getEndereco()->
                getMunicipio()->getEstado()->getUF());
            $inutilizacao->setAmbiente($nota->getAmbiente());
            $inutilizacao->setModelo($nota->getModelo());
        }
        $dom = $inutilizacao->getNode()->ownerDocument;
        $dom = $inutilizacao->assinar($dom);
        $dom = $inutilizacao->validar($dom);
        $dom = $inutilizacao->envia($dom);
        $this->setDocumento($dom);
        return $inutilizacao;
    }
    
    private function consulta()
    {
        $nota = $this->getNota();
        $agente = $this->getAgente();
        if (is_null($agente)) {
            if (is_null($nota)) {
                throw new \Exception('A nota não foi informada na tarefa de consulta "'.$this->getID().'"', 404);
            }
            $agente = new Situacao();
            $agente->setChave($nota->getID());
            $this->setAgente($agente);
        } elseif (!($agente instanceof Situacao) && !($agente instanceof Recibo)) {
            throw new \Exception('O agente informado não é uma consulta de situação e nem um recibo', 500);
        }
        if (!is_null($nota)) {
            $agente->setAmbiente($nota->getAmbiente());
            $agente->setModelo($nota->getModelo());
        }
        $retorno = $agente->consulta($this->getNota());
        if ($agente->isCancelado()) {
            // TODO: carregar assinatura do XML para evitar usar outro certificado
            $dom = $retorno->assinar();
            $dom = $retorno->validar($dom);
            // $dom = $retorno->getNode()->ownerDocument; // descomentar essa linha quando implementar
            // TODO: Fim do problema de assinatura
            $dom = $retorno->addInformacao($dom);
            $this->setDocumento($dom);
            $retorno = $retorno->getInformacao();
        }
        return $retorno;
    }
}
