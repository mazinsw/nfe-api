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

class Volume implements NodeInterface
{

    private $quantidade;
    private $especie;
    private $marca;
    private $numeracoes;
    private $peso;
    private $lacres;

    public function __construct($volume = array())
    {
        $this->fromArray($volume);
    }

    public function getQuantidade($normalize = false)
    {
        if (!$normalize) {
            return $this->quantidade;
        }
        return $this->quantidade;
    }

    public function setQuantidade($quantidade)
    {
        if (!is_null($quantidade)) {
            $quantidade = intval($quantidade);
        }
        $this->quantidade = $quantidade;
        return $this;
    }

    public function getEspecie($normalize = false)
    {
        if (!$normalize) {
            return $this->especie;
        }
        return $this->especie;
    }

    public function setEspecie($especie)
    {
        $this->especie = $especie;
        return $this;
    }

    public function getMarca($normalize = false)
    {
        if (!$normalize) {
            return $this->marca;
        }
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
        return $this;
    }

    public function getNumeracoes()
    {
        return $this->numeracoes;
    }

    public function setNumeracoes($numeracoes)
    {
        $this->numeracoes = $numeracoes;
        return $this;
    }

    public function addNumeracao($numeracao)
    {
        $this->numeracoes[] = $numeracao;
        return $this;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function setPeso($peso)
    {
        $this->peso = $peso;
        return $this;
    }

    public function getLacres()
    {
        return $this->lacres;
    }

    public function setLacres($lacres)
    {
        $this->lacres = $lacres;
        return $this;
    }

    public function addLacre($lacre)
    {
        $this->lacres[] = $lacre;
        return $this;
    }

    public function toArray()
    {
        $volume = array();
        $volume['quantidade'] = $this->getQuantidade();
        $volume['especie'] = $this->getEspecie();
        $volume['marca'] = $this->getMarca();
        $volume['numeracoes'] = $this->getNumeracoes();
        $volume['peso'] = $this->getPeso();
        $volume['lacres'] = $this->getLacres();
        return $volume;
    }

    public function fromArray($volume = array())
    {
        if ($volume instanceof Volume) {
            $volume = $volume->toArray();
        } elseif (!is_array($volume)) {
            return $this;
        }
        if (isset($volume['quantidade'])) {
            $this->setQuantidade($volume['quantidade']);
        } else {
            $this->setQuantidade(null);
        }
        if (isset($volume['especie'])) {
            $this->setEspecie($volume['especie']);
        } else {
            $this->setEspecie(null);
        }
        if (isset($volume['marca'])) {
            $this->setMarca($volume['marca']);
        } else {
            $this->setMarca(null);
        }
        if (!isset($volume['numeracoes']) || is_null($volume['numeracoes'])) {
            $this->setNumeracoes(array());
        } else {
            $this->setNumeracoes($volume['numeracoes']);
        }
        if (!isset($volume['peso']) || is_null($volume['peso'])) {
            $this->setPeso(new Peso());
        } else {
            $this->setPeso($volume['peso']);
        }
        if (!isset($volume['lacres']) || is_null($volume['lacres'])) {
            $this->setLacres(array());
        } else {
            $this->setLacres($volume['lacres']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'vol':$name);
        if (!is_null($this->getQuantidade())) {
            $element->appendChild($dom->createElement('qVol', $this->getQuantidade(true)));
        }
        if (!is_null($this->getEspecie())) {
            $element->appendChild($dom->createElement('esp', $this->getEspecie(true)));
        }
        if (!is_null($this->getMarca())) {
            $element->appendChild($dom->createElement('marca', $this->getMarca(true)));
        }
        $_numeracoes = $this->getNumeracoes();
        if (!empty($_numeracoes)) {
            $numeracoes = $dom->createElement('nVol', implode(', ', $_numeracoes));
            $element->appendChild($numeracoes);
        }
        if (!is_null($this->getPeso())) {
            $peso = $this->getPeso();
            $element->appendChild($dom->createElement('pesoL', $peso->getLiquido(true)));
            $element->appendChild($dom->createElement('pesoB', $peso->getBruto(true)));
        }
        $_lacres = $this->getLacres();
        if (!empty($_lacres)) {
            foreach ($_lacres as $_lacre) {
                $lacre = $_lacre->getNode();
                $lacre = $dom->importNode($lacre, true);
                $element->appendChild($lacre);
            }
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'vol':$name;
        if ($element->tagName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $_fields = $element->getElementsByTagName('qVol');
        $quantidade = null;
        if ($_fields->length > 0) {
            $quantidade = $_fields->item(0)->nodeValue;
        }
        $this->setQuantidade($quantidade);
        $_fields = $element->getElementsByTagName('esp');
        $especie = null;
        if ($_fields->length > 0) {
            $especie = $_fields->item(0)->nodeValue;
        }
        $this->setEspecie($especie);
        $_fields = $element->getElementsByTagName('marca');
        $marca = null;
        if ($_fields->length > 0) {
            $marca = $_fields->item(0)->nodeValue;
        }
        $this->setMarca($marca);
        $numeracoes = array();
        $_fields = $element->getElementsByTagName('nVol');
        if ($_fields->length > 0) {
            $volumes = $_fields->item(0)->nodeValue;
            if ($volumes != '') {
                $numeracoes = explode(', ', $volumes);
            }
        }
        $this->setNumeracoes($numeracoes);
        $_fields = $element->getElementsByTagName('pesoL');
        $peso = null;
        if ($_fields->length > 0) {
            $peso = new Peso();
            $peso->setLiquido($_fields->item(0)->nodeValue);
        }
        $_fields = $element->getElementsByTagName('pesoB');
        if ($_fields->length > 0) {
            if (is_null($peso)) {
                $peso = new Peso();
            }
            $peso->setBruto($_fields->item(0)->nodeValue);
        }
        $this->setPeso($peso);
        $lacres = array();
        $_fields = $element->getElementsByTagName('lacres');
        foreach ($_fields as $_item) {
            $lacre = new Lacre();
            $lacre->loadNode($_item, 'lacres');
            $lacres[] = $lacre;
        }
        $this->setLacres($lacres);
        return $element;
    }
}
