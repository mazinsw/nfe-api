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

use NFe\Common\Util;

class Retorno extends Status
{

    private $data_recebimento;

    public function __construct($retorno = array())
    {
        parent::__construct($retorno);
    }

    public function getDataRecebimento($normalize = false)
    {
        if (!$normalize || is_null($this->data_recebimento)) {
            return $this->data_recebimento;
        }
        return Util::toDateTime($this->data_recebimento);
    }

    public function setDataRecebimento($data_recebimento)
    {
        if (!is_null($data_recebimento) && !is_numeric($data_recebimento)) {
            $data_recebimento = strtotime($data_recebimento);
        }
        $this->data_recebimento = $data_recebimento;
        return $this;
    }

    /**
     * Informa se a nota foi autorizada no prazo ou fora do prazo
     */
    public function isAutorizado()
    {
        return in_array($this->getStatus(), array('100', '150'));
    }

    /**
     * Informa se a nota está cancelada
     */
    public function isCancelado()
    {
        return in_array($this->getStatus(), array('101', '151'));
    }

    /**
     * Informa se o lote já foi processado e já tem um protocolo
     */
    public function isProcessado()
    {
        return $this->getStatus() == '104';
    }

    /**
     * Informa se o lote foi recebido com sucesso
     */
    public function isRecebido()
    {
        return in_array($this->getStatus(), array('103', '105'));
    }

    /**
     * Informa se a nota foi denegada
     */
    public function isDenegada()
    {
        return in_array($this->getStatus(), array('110', '301', '302', '303'));
    }

    /**
     * Informa se a nota da consulta não foi autorizada ou se não existe
     */
    public function isInexistente()
    {
        return $this->getStatus() == '217';
    }

    public function toArray($recursive = false)
    {
        $retorno = parent::toArray($recursive);
        $retorno['data_recebimento'] = $this->getDataRecebimento($recursive);
        return $retorno;
    }

    public function fromArray($retorno = array())
    {
        if ($retorno instanceof Retorno) {
            $retorno = $retorno->toArray();
        } elseif (!is_array($retorno)) {
            return $this;
        }
        parent::fromArray($retorno);
        if (isset($retorno['data_recebimento'])) {
            $this->setDataRecebimento($retorno['data_recebimento']);
        } else {
            $this->setDataRecebimento(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name)?'':$name);
        $dom = $element->ownerDocument;
        $status = $element->getElementsByTagName('cStat')->item(0);
        if (!is_null($this->getDataRecebimento())) {
            Util::appendNode($element, 'dhRecbto', $this->getDataRecebimento(true), $status);
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'Retorno':$name;
        $retorno = parent::loadNode($element, $name);
        $this->setDataRecebimento(Util::loadNode($retorno, 'dhRecbto'));
        return $retorno;
    }
}
