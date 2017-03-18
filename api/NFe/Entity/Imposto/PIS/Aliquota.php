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
namespace NFe\Entity\Imposto\PIS;

use NFe\Common\Util;
use NFe\Entity\Imposto;

class Aliquota extends Imposto
{

    const TRIBUTACAO_NORMAL = 'normal';
    const TRIBUTACAO_DIFERENCIADA = 'diferenciada';

    public function __construct($pis = array())
    {
        parent::__construct($pis);
        $this->setGrupo(self::GRUPO_PIS);
    }

    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return parent::getTributacao();
        }
        switch (parent::getTributacao()) {
            case self::TRIBUTACAO_NORMAL:
                return '01';
            case self::TRIBUTACAO_DIFERENCIADA:
                return '02';
        }
        return parent::getTributacao($normalize);
    }

    public function toArray($recursive = false)
    {
        $pis = parent::toArray($recursive);
        return $pis;
    }

    public function fromArray($pis = array())
    {
        if ($pis instanceof Aliquota) {
            $pis = $pis->toArray();
        } elseif (!is_array($pis)) {
            return $this;
        }
        parent::fromArray($pis);
        if (is_null($this->getTributacao())) {
            $this->setTributacao(self::TRIBUTACAO_NORMAL);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'PISAliq':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pPIS', $this->getAliquota(true));
        Util::appendNode($element, 'vPIS', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'PISAliq':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBC',
                'Tag "vBC" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pPIS',
                'Tag "pPIS" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
