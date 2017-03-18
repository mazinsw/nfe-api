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

use NFe\Core\Nota;
use NFe\Common\Util;
use NFe\Common\Node;
use NFe\Entity\Estado;

/**
 * Status das respostas de envios para os servidores da SEFAZ
 */
class Status implements Node
{

    private $ambiente;
    private $versao;
    private $status;
    private $motivo;
    private $uf;

    public function __construct($status = array())
    {
        $this->fromArray($status);
    }

    /**
     * Identificação do Ambiente:
     * 1 - Produção
     * 2 - Homologação
     */
    public function getAmbiente($normalize = false)
    {
        if (!$normalize) {
            return $this->ambiente;
        }
        switch ($this->ambiente) {
            case Nota::AMBIENTE_PRODUCAO:
                return '1';
            case Nota::AMBIENTE_HOMOLOGACAO:
                return '2';
        }
        return $this->ambiente;
    }

    public function setAmbiente($ambiente)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Versão do Aplicativo que processou a NF-e
     */
    public function getVersao($normalize = false)
    {
        if (!$normalize) {
            return $this->versao;
        }
        return $this->versao;
    }

    public function setVersao($versao)
    {
        $this->versao = $versao;
        return $this;
    }

    /**
     * Código do status da mensagem enviada.
     */
    public function getStatus($normalize = false)
    {
        if (!$normalize) {
            return $this->status;
        }
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Descrição literal do status do serviço solicitado.
     */
    public function getMotivo($normalize = false)
    {
        if (!$normalize) {
            return $this->motivo;
        }
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
        return $this;
    }

    /**
     * código da UF de atendimento
     */
    public function getUF($normalize = false)
    {
        if (!$normalize || is_numeric($this->uf)) {
            return $this->uf;
        }

        $estado = new Estado();
        $estado->setUF($this->uf);
        $estado->checkCodigos();
        return $estado->getCodigo();
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    /**
     * Gera um número único com 15 dígitos
     * @return string Número com 15 dígitos
     */
    public static function genLote()
    {
        return substr(Util::padText(number_format(microtime(true)*1000000, 0, '', ''), 15), 0, 15);
    }

    public function toArray($recursive = false)
    {
        $status = array();
        $status['ambiente'] = $this->getAmbiente();
        $status['versao'] = $this->getVersao();
        $status['status'] = $this->getStatus();
        $status['motivo'] = $this->getMotivo();
        $status['uf'] = $this->getUF();
        return $status;
    }

    public function fromArray($status = array())
    {
        if ($status instanceof Status) {
            $status = $status->toArray();
        } elseif (!is_array($status)) {
            return $this;
        }
        if (isset($status['ambiente'])) {
            $this->setAmbiente($status['ambiente']);
        } else {
            $this->setAmbiente(null);
        }
        if (isset($status['versao'])) {
            $this->setVersao($status['versao']);
        } else {
            $this->setVersao(null);
        }
        if (isset($status['status'])) {
            $this->setStatus($status['status']);
        } else {
            $this->setStatus(null);
        }
        if (isset($status['motivo'])) {
            $this->setMotivo($status['motivo']);
        } else {
            $this->setMotivo(null);
        }
        if (isset($status['uf'])) {
            $this->setUF($status['uf']);
        } else {
            $this->setUF(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'Status':$name);
        Util::appendNode($element, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($element, 'verAplic', $this->getVersao(true));
        Util::appendNode($element, 'cStat', $this->getStatus(true));
        Util::appendNode($element, 'xMotivo', $this->getMotivo(true));
        if (!is_null($this->getUF())) {
            Util::appendNode($element, 'cUF', $this->getUF(true));
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'Status':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" do Status não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setAmbiente(
            Util::loadNode(
                $element,
                'tpAmb',
                'Tag "tpAmb" não encontrada no Status'
            )
        );
        $this->setVersao(
            Util::loadNode(
                $element,
                'verAplic',
                'Tag "verAplic" não encontrada no Status'
            )
        );
        $this->setStatus(
            Util::loadNode(
                $element,
                'cStat',
                'Tag "cStat" não encontrada no Status'
            )
        );
        $this->setMotivo(
            Util::loadNode(
                $element,
                'xMotivo',
                'Tag "xMotivo" não encontrada no Status'
            )
        );
        $this->setUF(Util::loadNode($element, 'cUF'));
        return $element;
    }
}
