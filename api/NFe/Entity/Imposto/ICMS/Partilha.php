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
namespace NFe\Entity\Imposto\ICMS;

use NFe\Common\Util;

/**
 * Partilha do ICMS entre a UF de origem e UF de destino ou a UF definida
 * na legislação
 * Operação interestadual para consumidor final com partilha
 * do ICMS  devido na operação entre a UF de origem e a UF do destinatário
 * ou ou a UF definida na legislação. (Ex. UF da concessionária de entrega
 * do  veículos)
 */
class Partilha extends Mista
{

    private $operacao;
    private $uf;

    public function __construct($partilha = array())
    {
        parent::__construct($partilha);
        $this->setTributacao('10');
    }

    /**
     * Percentual para determinação do valor  da Base de Cálculo da operação
     * própria.
     */
    public function getOperacao($normalize = false)
    {
        if (!$normalize) {
            return $this->operacao;
        }
        return Util::toFloat($this->operacao);
    }

    public function setOperacao($operacao)
    {
        $this->operacao = $operacao;
        return $this;
    }

    /**
     * Sigla da UF para qual é devido o ICMS ST da operação.
     */
    public function getUF($normalize = false)
    {
        if (!$normalize) {
            return $this->uf;
        }
        return $this->uf;
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $partilha = parent::toArray($recursive);
        $partilha['operacao'] = $this->getOperacao();
        $partilha['uf'] = $this->getUF();
        return $partilha;
    }

    public function fromArray($partilha = array())
    {
        if ($partilha instanceof Partilha) {
            $partilha = $partilha->toArray();
        } elseif (!is_array($partilha)) {
            return $this;
        }
        parent::fromArray($partilha);
        if (isset($partilha['operacao'])) {
            $this->setOperacao($partilha['operacao']);
        } else {
            $this->setOperacao(null);
        }
        if (isset($partilha['uf'])) {
            $this->setUF($partilha['uf']);
        } else {
            $this->setUF(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name)?'ICMSPart':$name);
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'pBCOp', $this->getOperacao(true));
        Util::appendNode($element, 'UFST', $this->getUF(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMSPart':$name;
        $element = parent::loadNode($element, $name);
        $this->setOperacao(
            Util::loadNode(
                $element,
                'pBCOp',
                'Tag "pBCOp" do campo "Operacao" não encontrada na Partilha'
            )
        );
        $this->setUF(
            Util::loadNode(
                $element,
                'UFST',
                'Tag "UFST" do campo "UF" não encontrada na Partilha'
            )
        );
        return $element;
    }
}
