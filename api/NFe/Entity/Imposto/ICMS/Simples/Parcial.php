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
 * Tributada pelo Simples Nacional sem permissão de crédito e com cobrança
 * do ICMS por substituição tributária
 */
class Parcial extends \NFe\Entity\Imposto\ICMS\Parcial
{

    public function __construct($parcial = array())
    {
        parent::__construct($parcial);
        $this->setTributacao('202');
    }

    public function toArray($recursive = false)
    {
        $parcial = parent::toArray($recursive);
        return $parcial;
    }

    public function fromArray($parcial = array())
    {
        if ($parcial instanceof Parcial) {
            $parcial = $parcial->toArray();
        } elseif (!is_array($parcial)) {
            return $this;
        }
        parent::fromArray($parcial);
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'IMCSSN202':$name);
        Util::appendNode($element, 'orig', $this->getOrigem(true));
        Util::appendNode($element, 'CSOSN', $this->getTributacao(true));
        Util::appendNode($element, 'modBCST', $this->getModalidade(true));
        Util::appendNode($element, 'pMVAST', $this->getMargem(true));
        Util::appendNode($element, 'pRedBCST', $this->getReducao(true));
        Util::appendNode($element, 'vBCST', $this->getBase(true));
        Util::appendNode($element, 'pICMSST', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSST', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IMCSSN202':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
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
                'CSOSN',
                'Tag "CSOSN" do campo "Tributacao" não encontrada'
            )
        );
        $this->setModalidade(
            Util::loadNode(
                $element,
                'modBCST',
                'Tag "modBCST" do campo "Modalidade" não encontrada'
            )
        );
        $this->setMargem(
            Util::loadNode(
                $element,
                'pMVAST',
                'Tag "pMVAST" do campo "Margem" não encontrada'
            )
        );
        $this->setReducao(
            Util::loadNode(
                $element,
                'pRedBCST',
                'Tag "pRedBCST" do campo "Reducao" não encontrada'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCST',
                'Tag "vBCST" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pICMSST',
                'Tag "pICMSST" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
