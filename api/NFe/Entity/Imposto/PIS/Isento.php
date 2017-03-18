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

class Isento extends Imposto
{

    /**
     * Código de Situação Tributária do PIS.
     * 04 - Operação Tributável -
     * Tributação Monofásica - (Alíquota Zero);
     * 06 - Operação Tributável -
     * Alíquota Zero;
     * 07 - Operação Isenta da contribuição;
     * 08 - Operação Sem
     * Incidência da contribuição;
     * 09 - Operação com suspensão da contribuição;
     */
    const TRIBUTACAO_MONOFASICA = 'monofasica';
    const TRIBUTACAO_ZERO = 'zero';
    const TRIBUTACAO_ISENTA = 'isenta';
    const TRIBUTACAO_INCIDENCIA = 'incidencia';
    const TRIBUTACAO_SUSPENSAO = 'suspensao';

    public function __construct($pis = array())
    {
        parent::__construct($pis);
        $this->setGrupo(self::GRUPO_PIS);
        $this->setBase(0.00);
        $this->setAliquota(0.0);
    }

    /**
     * Código de Situação Tributária do PIS.
     * 04 - Operação Tributável -
     * Tributação Monofásica - (Alíquota Zero);
     * 06 - Operação Tributável -
     * Alíquota Zero;
     * 07 - Operação Isenta da contribuição;
     * 08 - Operação Sem
     * Incidência da contribuição;
     * 09 - Operação com suspensão da contribuição;
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return parent::getTributacao();
        }
        switch (parent::getTributacao()) {
            case self::TRIBUTACAO_MONOFASICA:
                return '04';
            case self::TRIBUTACAO_ZERO:
                return '06';
            case self::TRIBUTACAO_ISENTA:
                return '07';
            case self::TRIBUTACAO_INCIDENCIA:
                return '08';
            case self::TRIBUTACAO_SUSPENSAO:
                return '09';
        }
        return parent::getTributacao($normalize);
    }
    
    /**
     * Altera o valor da Tributacao para o informado no parâmetro
     * @param mixed $tributacao novo valor para Tributacao
     * @return Isento A própria instância da classe
     */
    public function setTributacao($tributacao)
    {
        switch ($tributacao) {
            case '04':
                $tributacao = self::TRIBUTACAO_MONOFASICA;
                break;
            case '06':
                $tributacao = self::TRIBUTACAO_ZERO;
                break;
            case '07':
                $tributacao = self::TRIBUTACAO_ISENTA;
                break;
            case '08':
                $tributacao = self::TRIBUTACAO_INCIDENCIA;
                break;
            case '09':
                $tributacao = self::TRIBUTACAO_SUSPENSAO;
                break;
        }
        return parent::setTributacao($tributacao);
    }

    public function toArray($recursive = false)
    {
        $pis = parent::toArray($recursive);
        return $pis;
    }

    public function fromArray($pis = array())
    {
        if ($pis instanceof Isento) {
            $pis = $pis->toArray();
        } elseif (!is_array($pis)) {
            return $this;
        }
        parent::fromArray($pis);
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'PISNT':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'PISNT':$name;
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
        return $element;
    }
}
