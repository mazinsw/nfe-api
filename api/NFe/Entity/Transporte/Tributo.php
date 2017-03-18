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

use NFe\Common\Util;
use NFe\Entity\Imposto;
use NFe\Entity\Municipio;

/**
 * ICMS retido do Transportador
 */
class Tributo extends Imposto
{

    private $servico;
    private $cfop;
    private $municipio;

    public function __construct($tributo = array())
    {
        parent::__construct($tributo);
    }

    public function getServico($normalize = false)
    {
        if (!$normalize) {
            return $this->servico;
        }
        return Util::toCurrency($this->servico);
    }

    public function setServico($servico)
    {
        $this->servico = $servico;
        return $this;
    }

    public function getCFOP($normalize = false)
    {
        if (!$normalize) {
            return $this->cfop;
        }
        return $this->cfop;
    }

    public function setCFOP($cfop)
    {
        $this->cfop = $cfop;
        return $this;
    }

    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $tributo = parent::toArray($recursive);
        $tributo['servico'] = $this->getServico();
        $tributo['cfop'] = $this->getCFOP();
        if (!is_null($this->getMunicipio()) && $recursive) {
            $tributo['municipio'] = $this->getMunicipio()->toArray($recursive);
        } else {
            $tributo['municipio'] = $this->getMunicipio();
        }
        return $tributo;
    }

    public function fromArray($tributo = array())
    {
        if ($tributo instanceof Tributo) {
            $tributo = $tributo->toArray();
        } elseif (!is_array($tributo)) {
            return $this;
        }
        parent::fromArray($tributo);
        if (isset($tributo['servico'])) {
            $this->setServico($tributo['servico']);
        } else {
            $this->setServico(null);
        }
        if (isset($tributo['cfop'])) {
            $this->setCFOP($tributo['cfop']);
        } else {
            $this->setCFOP(null);
        }
        if (!isset($tributo['municipio']) || is_null($tributo['municipio'])) {
            $this->setMunicipio(new Municipio());
        } else {
            $this->setMunicipio($tributo['municipio']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'retTransp':$name);
        Util::appendNode($element, 'vServ', $this->getServico(true));
        Util::appendNode($element, 'vBCRet', $this->getBase(true));
        Util::appendNode($element, 'pICMSRet', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSRet', $this->getValor(true));
        Util::appendNode($element, 'CFOP', $this->getCFOP(true));
        if (is_null($this->getMunicipio())) {
            return $element;
        }
        $municipio = $this->getMunicipio();
        $municipio->checkCodigos();
        Util::appendNode($element, 'cMunFG', $municipio->getCodigo(true));
        return $element;
    }


    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'retTransp':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" do Tributo não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setServico(
            Util::loadNode(
                $element,
                'vServ',
                'Tag "vServ" do campo "Servico" não encontrada no Tributo'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCRet',
                'Tag "vBCRet" do campo "Base" não encontrada no Tributo'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pICMSRet',
                'Tag "pICMSRet" do campo "Aliquota" não encontrada no Tributo'
            )
        );
        $this->setCFOP(
            Util::loadNode(
                $element,
                'CFOP',
                'Tag "CFOP" do campo "CFOP" não encontrada no Tributo'
            )
        );
        $municipio = null;
        $codigo = Util::loadNode($element, 'cMunFG');
        if (!is_null($codigo)) {
            $municipio = new Municipio();
            $municipio->setCodigo($codigo);
        }
        $this->setMunicipio($municipio);
        return $element;
    }
}
