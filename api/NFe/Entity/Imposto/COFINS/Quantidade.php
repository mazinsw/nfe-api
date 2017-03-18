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
namespace NFe\Entity\Imposto\COFINS;

use NFe\Common\Util;
use NFe\Entity\Imposto;

class Quantidade extends Imposto
{

    public function __construct($cofins = array())
    {
        parent::__construct($cofins);
        $this->setGrupo(self::GRUPO_COFINS);
        $this->setTributacao('03');
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

    /**
     * Calcula o valor do imposto com base na quantidade e no valor da aliquota
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->getQuantidade() * $this->getAliquota();
        }
        $valor = $this->getValor();
        return Util::toCurrency($valor);
    }

    public function toArray($recursive = false)
    {
        $cofins = parent::toArray($recursive);
        return $cofins;
    }

    public function fromArray($cofins = array())
    {
        if ($cofins instanceof Quantidade) {
            $cofins = $cofins->toArray();
        } elseif (!is_array($cofins)) {
            return $this;
        }
        parent::fromArray($cofins);
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'COFINSQtde':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'qBCProd', $this->getQuantidade(true));
        Util::appendNode($element, 'vAliqProd', $this->getAliquota(true));
        Util::appendNode($element, 'vCOFINS', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'COFINSQtde':$name;
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
                'qBCProd',
                'Tag "qBCProd" do campo "Quantidade" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'vAliqProd',
                'Tag "vAliqProd" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
