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
namespace NFe\Entity\Imposto\ICMS\Simples;

use NFe\Common\Util;

/**
 * Tributada pelo Simples Nacional com permissão de crédito e com cobrança
 * do ICMS por substituição tributária
 */
class Cobranca extends Parcial
{

    private $normal;

    public function __construct($cobranca = array())
    {
        parent::__construct($cobranca);
        $this->setTributacao('201');
    }

    public function getNormal()
    {
        return $this->normal;
    }

    public function setNormal($normal)
    {
        $this->normal = $normal;
        return $this;
    }

    /**
     * Calcula o valor do imposto com base na aliquota e valor base
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return ($this->getBase() * $this->getAliquota()) / 100.0 - $this->getNormal()->getValor();
        }
        $valor = $this->getValor();
        return Util::toCurrency($valor);
    }

    /**
     * Obtém o valor total do imposto
     */
    public function getTotal($normalize = false)
    {
        return $this->getNormal()->getValor($normalize);
    }

    public function toArray($recursive = false)
    {
        $cobranca = parent::toArray($recursive);
        if (!is_null($this->getNormal()) && $recursive) {
            $cobranca['normal'] = $this->getNormal()->toArray($recursive);
        } else {
            $cobranca['normal'] = $this->getNormal();
        }
        return $cobranca;
    }

    public function fromArray($cobranca = array())
    {
        if ($cobranca instanceof Cobranca) {
            $cobranca = $cobranca->toArray();
        } elseif (!is_array($cobranca)) {
            return $this;
        }
        parent::fromArray($cobranca);
        if (!isset($cobranca['normal']) || is_null($cobranca['normal'])) {
            $this->setNormal(new Normal());
        } else {
            $this->setNormal($cobranca['normal']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = $this->getNormal()->getNode(is_null($name)?'ICMSSN201':$name);
        if (is_null($this->getModalidade())) {
            return $element;
        }
        $dom = $element->ownerDocument;
        $parcial = parent::getNode(is_null($name)?'ICMSSN201':$name);
        if (is_null($this->getNormal()->getModalidade())) {
            return $parcial;
        }
        foreach ($parcial->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $element->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                $element->replaceChild($node, $list->item(0));
            } else {
                $element->appendChild($node);
            }
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMSSN201':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $normal = $this->getNormal();
        if (is_null($normal)) {
            $normal = new Normal();
        }
        $this->setNormal($normal);
        $_fields = $element->getElementsByTagName('modBCST');
        if ($_fields->length == 0) {
            $normal->loadNode($element, $name);
            return $element;
        }
        $element = parent::loadNode($element, $name);
        $_fields = $element->getElementsByTagName('pCredSN');
        if ($_fields->length == 0) {
            return $element;
        }
        $normal->setModalidade(Normal::MODALIDADE_OPERACAO); // forçar escrita
        $normal->loadNode($element, $name);
        return $element;
    }
}
