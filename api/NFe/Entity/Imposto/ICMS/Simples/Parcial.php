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

    public function toArray()
    {
        $parcial = parent::toArray();
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
        $element->appendChild($dom->createElement('orig', $this->getOrigem(true)));
        $element->appendChild($dom->createElement('CSOSN', $this->getTributacao(true)));
        $element->appendChild($dom->createElement('modBCST', $this->getModalidade(true)));
        $element->appendChild($dom->createElement('pMVAST', $this->getMargem(true)));
        $element->appendChild($dom->createElement('pRedBCST', $this->getReducao(true)));
        $element->appendChild($dom->createElement('vBCST', $this->getBase(true)));
        $element->appendChild($dom->createElement('pICMSST', $this->getAliquota(true)));
        $element->appendChild($dom->createElement('vICMSST', $this->getValor(true)));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IMCSSN202':$name;
        if ($element->tagName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $_fields = $element->getElementsByTagName('orig');
        if ($_fields->length > 0) {
            $origem = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "orig" do campo "Origem" não encontrada', 404);
        }
        $this->setOrigem($origem);
        $_fields = $element->getElementsByTagName('CSOSN');
        if ($_fields->length > 0) {
            $tributacao = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "CSOSN" do campo "Tributacao" não encontrada', 404);
        }
        $this->setTributacao($tributacao);
        $_fields = $element->getElementsByTagName('modBCST');
        if ($_fields->length > 0) {
            $modalidade = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "modBCST" do campo "Modalidade" não encontrada', 404);
        }
        $this->setModalidade($modalidade);
        $_fields = $element->getElementsByTagName('pMVAST');
        if ($_fields->length > 0) {
            $margem = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "pMVAST" do campo "Margem" não encontrada', 404);
        }
        $this->setMargem($margem);
        $_fields = $element->getElementsByTagName('pRedBCST');
        if ($_fields->length > 0) {
            $reducao = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "pRedBCST" do campo "Reducao" não encontrada', 404);
        }
        $this->setReducao($reducao);
        $_fields = $element->getElementsByTagName('vBCST');
        if ($_fields->length > 0) {
            $base = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "vBCST" do campo "Base" não encontrada', 404);
        }
        $this->setBase($base);
        $_fields = $element->getElementsByTagName('pICMSST');
        if ($_fields->length > 0) {
            $aliquota = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "pICMSST" do campo "Aliquota" não encontrada', 404);
        }
        $this->setAliquota($aliquota);
        return $element;
    }
}
