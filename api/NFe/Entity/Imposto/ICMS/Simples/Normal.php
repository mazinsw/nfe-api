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
 * Tributada pelo Simples Nacional com permissão de crédito
 */
class Normal extends \NFe\Entity\Imposto\ICMS\Normal
{

    public function __construct($normal = array())
    {
        parent::__construct($normal);
        $this->setTributacao('101');
    }

    public function toArray()
    {
        $normal = parent::toArray();
        return $normal;
    }

    public function fromArray($normal = array())
    {
        if ($normal instanceof Normal) {
            $normal = $normal->toArray();
        } elseif (!is_array($normal)) {
            return $this;
        }
        parent::fromArray($normal);
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'ICMSSN101':$name);
        $element->appendChild($dom->createElement('orig', $this->getOrigem(true)));
        $element->appendChild($dom->createElement('CSOSN', $this->getTributacao(true)));
        $element->appendChild($dom->createElement('pCredSN', $this->getAliquota(true)));
        $element->appendChild($dom->createElement('vCredICMSSN', $this->getValor(true)));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMSSN101':$name;
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
        $_fields = $element->getElementsByTagName('pCredSN');
        if ($_fields->length > 0) {
            $aliquota = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "pCredSN" do campo "Aliquota" não encontrada', 404);
        }
        $this->setAliquota($aliquota);
        $_fields = $element->getElementsByTagName('vCredICMSSN');
        if ($_fields->length == 0) {
            throw new \Exception('Tag "vCredICMSSN" do campo "Valor" não encontrada', 404);
        }
        $valor = $_fields->item(0)->nodeValue;
        $this->setBase($valor * 100.0 / $this->getAliquota());
        return $element;
    }
}
