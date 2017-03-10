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

/**
 * Tributação pelo ICMS
 * 00 - Tributada integralmente, estende de Normal
 */
class Integral extends Normal
{

    public function __construct($integral = array())
    {
        parent::__construct($integral);
        $this->setTributacao('00');
    }

    public function toArray($recursive = false)
    {
        $integral = parent::toArray($recursive);
        return $integral;
    }

    public function fromArray($integral = array())
    {
        if ($integral instanceof Integral) {
            $integral = $integral->toArray();
        } elseif (!is_array($integral)) {
            return $this;
        }
        parent::fromArray($integral);
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name)?'ICMS00':$name);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMS00':$name;
        $element = parent::loadNode($element, $name);
        return $element;
    }
}
