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
namespace NFe\Entity\Transporte;

use NFe\Common\Node;
use NFe\Common\Util;

class Veiculo implements Node
{

    private $placa;
    private $uf;
    private $rntc;

    public function __construct($veiculo = array())
    {
        $this->fromArray($veiculo);
    }

    public function getPlaca($normalize = false)
    {
        if (!$normalize) {
            return $this->placa;
        }
        return $this->placa;
    }

    public function setPlaca($placa)
    {
        $this->placa = $placa;
        return $this;
    }

    public function getUF($normalize = false)
    {
        if (!$normalize) {
            return $this->uf;
        }
        return $this->uf;
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    public function getRNTC($normalize = false)
    {
        if (!$normalize) {
            return $this->rntc;
        }
        return $this->rntc;
    }

    public function setRNTC($rntc)
    {
        $this->rntc = $rntc;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $veiculo = array();
        $veiculo['placa'] = $this->getPlaca();
        $veiculo['uf'] = $this->getUF();
        $veiculo['rntc'] = $this->getRNTC();
        return $veiculo;
    }

    public function fromArray($veiculo = array())
    {
        if ($veiculo instanceof Veiculo) {
            $veiculo = $veiculo->toArray();
        } elseif (!is_array($veiculo)) {
            return $this;
        }
        if (isset($veiculo['placa'])) {
            $this->setPlaca($veiculo['placa']);
        } else {
            $this->setPlaca(null);
        }
        if (isset($veiculo['uf'])) {
            $this->setUF($veiculo['uf']);
        } else {
            $this->setUF(null);
        }
        if (isset($veiculo['rntc'])) {
            $this->setRNTC($veiculo['rntc']);
        } else {
            $this->setRNTC(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'veicTransp':$name);
        Util::appendNode($element, 'placa', $this->getPlaca(true));
        Util::appendNode($element, 'UF', $this->getUF(true));
        if (!is_null($this->getRNTC())) {
            Util::appendNode($element, 'RNTC', $this->getRNTC(true));
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'veicTransp':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" do Veiculo não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setPlaca(
            Util::loadNode(
                $element,
                'placa',
                'Tag "placa" do campo "Placa" não encontrada no Veiculo'
            )
        );
        $this->setUF(
            Util::loadNode(
                $element,
                'UF',
                'Tag "UF" do campo "UF" não encontrada no Veiculo'
            )
        );
        $this->setRNTC(Util::loadNode($element, 'RNTC'));
        return $element;
    }
}
