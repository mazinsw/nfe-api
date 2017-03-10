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

use NFe\Common\Util;

/**
 * Peso de um produto, utilizado no cálculo do frete
 */
class Peso
{

    private $liquido;
    private $bruto;

    public function __construct($peso = array())
    {
        $this->fromArray($peso);
    }

    /**
     * Peso liquido
     */
    public function getLiquido($normalize = false)
    {
        if (!$normalize) {
            return $this->liquido;
        }
        return Util::toFloat($this->liquido, 3);
    }

    public function setLiquido($liquido)
    {
        $this->liquido = $liquido;
        return $this;
    }

    /**
     * Peso bruto
     */
    public function getBruto($normalize = false)
    {
        if (!$normalize) {
            return $this->bruto;
        }
        return Util::toFloat($this->bruto, 3);
    }

    public function setBruto($bruto)
    {
        $this->bruto = $bruto;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $peso = array();
        $peso['liquido'] = $this->getLiquido();
        $peso['bruto'] = $this->getBruto();
        return $peso;
    }

    public function fromArray($peso = array())
    {
        if ($peso instanceof Peso) {
            $peso = $peso->toArray();
        } elseif (!is_array($peso)) {
            return $this;
        }
        if (isset($peso['liquido'])) {
            $this->setLiquido($peso['liquido']);
        } else {
            $this->setLiquido(null);
        }
        if (isset($peso['bruto'])) {
            $this->setBruto($peso['bruto']);
        } else {
            $this->setBruto(null);
        }
        return $this;
    }
}
