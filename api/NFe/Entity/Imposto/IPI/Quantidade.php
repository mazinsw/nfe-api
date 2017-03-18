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
namespace NFe\Entity\Imposto\IPI;

use NFe\Common\Util;
use NFe\Entity\Imposto;

/**
 * Quantidade x valor Unidade de Produto
 */
class Quantidade extends Imposto
{

    public function __construct($quantidade = array())
    {
        parent::__construct($quantidade);
        $this->setGrupo(self::GRUPO_IPI);
    }

    public function getQuantidade($normalize = false)
    {
        if (!$normalize) {
            return $this->getBase();
        }
        return Util::toFloat($this->getBase());
    }

    public function setQuantidade($quantidade)
    {
        return $this->setBase($quantidade);
    }

    public function getPreco($normalize = false)
    {
        if (!$normalize) {
            return $this->getAliquota();
        }
        return Util::toCurrency($this->getPreco(), 4);
    }

    public function setPreco($preco)
    {
        return $this->setAliquota($preco);
    }

    /**
     * Calcula o valor do imposto com base na quantidade e no preço
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->getQuantidade() * $this->getPreco();
        }
        return Util::toCurrency($this->getValor());
    }

    public function toArray($recursive = false)
    {
        $quantidade = parent::toArray($recursive);
        return $quantidade;
    }

    public function fromArray($quantidade = array())
    {
        if ($quantidade instanceof Quantidade) {
            $quantidade = $quantidade->toArray();
        } elseif (!is_array($quantidade)) {
            return $this;
        }
        parent::fromArray($quantidade);
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'IPITrib':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'qUnid', $this->getQuantidade(true));
        Util::appendNode($element, 'vUnid', $this->getPreco(true));
        Util::appendNode($element, 'vIPI', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IPITrib':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setQuantidade(
            Util::loadNode(
                $element,
                'qUnid',
                'Tag "qUnid" do campo "Quantidade" não encontrada'
            )
        );
        $this->setPreco(
            Util::loadNode(
                $element,
                'vUnid',
                'Tag "vUnid" do campo "Preco" não encontrada'
            )
        );
        return $element;
    }
}
