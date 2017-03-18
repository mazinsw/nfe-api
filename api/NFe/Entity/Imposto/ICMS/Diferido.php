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
 * Tributção pelo ICMS
 * 51 - Diferimento
 * A exigência do preenchimento das
 * informações do ICMS diferido fica à critério de cada UF, estende de
 * Reducao
 */
class Diferido extends Reducao
{

    private $diferimento;

    public function __construct($diferido = array())
    {
        parent::__construct($diferido);
        $this->setTributacao('51');
    }

    /**
     * Percentual do diferemento
     */
    public function getDiferimento($normalize = false)
    {
        if (!$normalize) {
            return $this->diferimento;
        }
        return Util::toFloat($this->diferimento);
    }

    public function setDiferimento($diferimento)
    {
        $this->diferimento = $diferimento;
        return $this;
    }

    /**
     * Valor do ICMS da Operação
     */
    public function getOperacao($normalize = false)
    {
        if (!$normalize) {
            return $this->getReduzido() * $this->getAliquota() / 100.0;
        }
        return Util::toCurrency($this->getOperacao());
    }

    /**
     * Valor do ICMS do diferimento
     */
    public function getDiferido($normalize = false)
    {
        if (!$normalize) {
            return $this->getDiferimento() * $this->getOperacao() / 100.0;
        }
        return Util::toCurrency($this->getDiferido());
    }

    /**
     * Calcula o valor do imposto
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->getOperacao() - $this->getDiferido();
        }
        return Util::toCurrency($this->getValor());
    }

    public function toArray($recursive = false)
    {
        $diferido = parent::toArray($recursive);
        $diferido['diferimento'] = $this->getDiferimento();
        return $diferido;
    }

    public function fromArray($diferido = array())
    {
        if ($diferido instanceof Diferido) {
            $diferido = $diferido->toArray();
        } elseif (!is_array($diferido)) {
            return $this;
        }
        parent::fromArray($diferido);
        if (isset($diferido['diferimento'])) {
            $this->setDiferimento($diferido['diferimento']);
        } else {
            $this->setDiferimento(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        if (is_null($this->getDiferimento())) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $element = $dom->createElement(is_null($name)?'ICMS51':$name);
            Util::appendNode($element, 'orig', $this->getOrigem(true));
            Util::appendNode($element, 'CST', $this->getTributacao(true));
            return $element;
        }
        $element = parent::getNode(is_null($name)?'ICMS51':$name);
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'vICMSOp', $this->getOperacao(true));
        Util::appendNode($element, 'pDif', $this->getDiferimento(true));
        Util::appendNode($element, 'vICMSDif', $this->getDiferido(true));
        if (Util::isEqual(floatval($this->getReducao()), 0.0)) {
            $item = $element->getElementsByTagName('pRedBC')->item(0);
            $element->removeChild($item);
        }
        if (Util::isEqual(floatval($this->getDiferimento()), 100.0)) {
            $item = $element->getElementsByTagName('vICMS')->item(0);
            $element->removeChild($item);
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMS51':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $dom = $element->ownerDocument;
        $element = $dom->importNode($element, true);
        $_dif = $element->getElementsByTagName('pDif');
        if ($_dif->length > 0) {
            $node_added = false;
            $save_element = $element;
            $_fields = $element->getElementsByTagName('pRedBC');
            if ($_fields->length == 0) {
                Util::appendNode($element, 'pRedBC', '0.0000');
                $node_added = true;
            }
            $element = parent::loadNode($element, $name);
            if ($node_added) {
                $item = $save_element->getElementsByTagName('pRedBC')->item(0);
                $save_element->removeChild($item);
            }
            $diferimento = $_dif->item(0)->nodeValue;
            $this->setDiferimento($diferimento);
            return $element;
        }
        $this->setOrigem(
            Util::loadNode(
                $element,
                'orig',
                'Tag "orig" do campo "Origem" não encontrada'
            )
        );
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setDiferimento(null);
        return $element;
    }
}
