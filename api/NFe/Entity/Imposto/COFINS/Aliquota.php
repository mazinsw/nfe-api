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
namespace NFe\Entity\Imposto\COFINS;

use NFe\Common\Util;
use NFe\Entity\Imposto;

class Aliquota extends Imposto
{

    const TRIBUTACAO_NORMAL = 'normal';
    const TRIBUTACAO_DIFERENCIADA = 'diferenciada';

    public function __construct($cofins = array())
    {
        parent::__construct($cofins);
        $this->setGrupo(self::GRUPO_COFINS);
    }

    /**
     * Código de Situação Tributária do COFINS.
     * 01 – Operação Tributável -
     * Base de Cálculo = Valor da Operação Alíquota Normal (Cumulativo/Não
     * Cumulativo);
     * 02 - Operação Tributável - Base de Calculo = Valor da
     * Operação (Alíquota Diferenciada);
     * @param boolean $normalize informa se a tributacao deve estar no formato do XML
     * @return mixed tributacao da Aliquota
     */
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

    /**
     * Altera o valor da Tributacao para o informado no parâmetro
     * @param mixed $tributacao novo valor para Tributacao
     * @return Aliquota A própria instância da classe
     */
    public function setTributacao($tributacao)
    {
        switch ($tributacao) {
            case '01':
                $tributacao = self::TRIBUTACAO_NORMAL;
                break;
            case '02':
                $tributacao = self::TRIBUTACAO_DIFERENCIADA;
                break;
        }
        return parent::setTributacao($tributacao);
    }

    public function toArray($recursive = false)
    {
        $cofins = parent::toArray($recursive);
        return $cofins;
    }

    public function fromArray($cofins = array())
    {
        if ($cofins instanceof Aliquota) {
            $cofins = $cofins->toArray();
        } elseif (!is_array($cofins)) {
            return $this;
        }
        parent::fromArray($cofins);
        if (is_null($this->getTributacao())) {
            $this->setTributacao(self::TRIBUTACAO_NORMAL);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'COFINSAliq':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pCOFINS', $this->getAliquota(true));
        Util::appendNode($element, 'vCOFINS', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'COFINSAliq':$name;
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
                'pCOFINS',
                'Tag "pCOFINS" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
