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

use NFe\Common\Util;

/**
 * Classe base do ICMS normal, estende de ICMS\Base
 */
class Normal extends Base
{

    const MODALIDADE_AGREGADO = 'agregado';
    const MODALIDADE_PAUTA = 'pauta';
    const MODALIDADE_TABELADO = 'tabelado';
    const MODALIDADE_OPERACAO = 'operacao';

    private $modalidade;

    public function __construct($normal = array())
    {
        parent::__construct($normal);
    }

    public function getModalidade($normalize = false)
    {
        if (!$normalize) {
            return $this->modalidade;
        }
        switch ($this->modalidade) {
            case self::MODALIDADE_AGREGADO:
                return '0';
            case self::MODALIDADE_PAUTA:
                return '1';
            case self::MODALIDADE_TABELADO:
                return '2';
            case self::MODALIDADE_OPERACAO:
                return '3';
        }
        return $this->modalidade;
    }

    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $normal = parent::toArray($recursive);
        $normal['modalidade'] = $this->getModalidade();
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
        if (isset($normal['modalidade'])) {
            $this->setModalidade($normal['modalidade']);
        } else {
            $this->setModalidade(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'IMCS':$name);
        Util::appendNode($element, 'orig', $this->getOrigem(true));
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'modBC', $this->getModalidade(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pICMS', $this->getAliquota(true));
        Util::appendNode($element, 'vICMS', $this->getValor(true));
        return $element;
    }


    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IMCS':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
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
        $this->setModalidade(
            Util::loadNode(
                $element,
                'modBC',
                'Tag "modBC" do campo "Modalidade" não encontrada'
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
                'pICMS',
                'Tag "pICMS" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
