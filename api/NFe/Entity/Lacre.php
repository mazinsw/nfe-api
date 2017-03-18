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
namespace NFe\Entity;

use NFe\Common\Node;
use NFe\Common\Util;

/**
 * Lacre do volume
 */
class Lacre implements Node
{

    private $numero;

    public function __construct($lacre = array())
    {
        $this->fromArray($lacre);
    }

    /**
     * Número do lacre
     */
    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $numero = intval($numero);
        $this->numero = $numero;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $lacre = array();
        $lacre['numero'] = $this->getNumero();
        return $lacre;
    }

    public function fromArray($lacre = array())
    {
        if ($lacre instanceof Lacre) {
            $lacre = $lacre->toArray();
        } elseif (!is_array($lacre)) {
            return $this;
        }
        if (isset($lacre['numero'])) {
            $this->setNumero($lacre['numero']);
        } else {
            $this->setNumero(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'lacres':$name);
        Util::appendNode($element, 'nLacre', $this->getNumero(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'lacres':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setNumero(
            Util::loadNode(
                $element,
                'nLacre',
                'Tag "nLacre" do campo "Numero" não encontrada'
            )
        );
        return $element;
    }
}
