<?php

/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
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
 * Tributação pelo ICMS
 * 90 - Outras, estende de Normal
 */
class Generico extends Mista
{
    public function __construct($generico = [])
    {
        parent::__construct($generico);
    }

    public function toArray($recursive = false)
    {
        $generico = parent::toArray($recursive);
        return $generico;
    }

    public function fromArray($generico = [])
    {
        if ($generico instanceof Generico) {
            $generico = $generico->toArray();
        } elseif (!is_array($generico)) {
            return $this;
        }
        parent::fromArray($generico);
        if (!isset($generico['tributacao'])) {
            $this->setTributacao('90');
        }
        $this->getNormal()->setTributacao('90');
        return $this;
    }

    public function getNode($name = null)
    {
        if (is_null($this->getModalidade()) && is_null($this->getNormal()->getModalidade())) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $element = $dom->createElement(is_null($name) ? 'ICMS90' : $name);
            Util::appendNode($element, 'orig', $this->getOrigem(true));
            Util::appendNode($element, 'CST', $this->getTributacao(true));
            return $element;
        }
        $element = parent::getNode(is_null($name) ? 'ICMS90' : $name);
        $dom = $element->ownerDocument;
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMS90' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $_mod = $element->getElementsByTagName('modBC');
        $_mod_st = $element->getElementsByTagName('modBCST');
        if ($_mod->length > 0 || $_mod_st->length > 0) {
            $element = parent::loadNode($element, $name);
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
        return $element;
    }
}
