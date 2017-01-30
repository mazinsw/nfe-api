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
namespace Imposto\ICMS;

use Util;
use Exception;
use DOMDocument;

/**
 * Tributação pelo ICMS
 * 30 - Isenta ou não tributada e com cobrança do ICMS
 * por substituição tributária, estende de Base
 */
class Parcial extends Base
{

    /**
     * Modalidade de determinação da BC do ICMS ST:
     * 0 – Preço tabelado ou
     * máximo  sugerido;
     * 1 - Lista Negativa (valor);
     * 2 - Lista Positiva
     * (valor);
     * 3 - Lista Neutra (valor);
     * 4 - Margem Valor Agregado (%);
     * 5 -
     * Pauta (valor).
     */
    const MODALIDADE_TABELADO = 'tabelado';
    const MODALIDADE_NEGATIVO = 'negativo';
    const MODALIDADE_POSITIVO = 'positivo';
    const MODALIDADE_NEUTRO = 'neutro';
    const MODALIDADE_AGREGADO = 'agregado';
    const MODALIDADE_PAUTA = 'pauta';

    private $modalidade;
    private $margem;
    private $reducao;

    public function __construct($parcial = array())
    {
        parent::__construct($parcial);
        $this->setTributacao('30');
    }

    /**
     * Modalidade de determinação da BC do ICMS ST:
     * 0 – Preço tabelado ou
     * máximo  sugerido;
     * 1 - Lista Negativa (valor);
     * 2 - Lista Positiva
     * (valor);
     * 3 - Lista Neutra (valor);
     * 4 - Margem Valor Agregado (%);
     * 5 -
     * Pauta (valor).
     */
    public function getModalidade($normalize = false)
    {
        if (!$normalize) {
            return $this->modalidade;
        }
        switch ($this->modalidade) {
            case self::MODALIDADE_TABELADO:
                return '0';
            case self::MODALIDADE_NEGATIVO:
                return '1';
            case self::MODALIDADE_POSITIVO:
                return '2';
            case self::MODALIDADE_NEUTRO:
                return '3';
            case self::MODALIDADE_AGREGADO:
                return '4';
            case self::MODALIDADE_PAUTA:
                return '5';
        }
        return $this->modalidade;
    }

    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
        return $this;
    }

    public function getMargem($normalize = false)
    {
        if (!$normalize) {
            return $this->margem;
        }
        return Util::toFloat($this->margem);
    }

    public function setMargem($margem)
    {
        $this->margem = $margem;
        return $this;
    }

    public function getReducao($normalize = false)
    {
        if (!$normalize) {
            return $this->reducao;
        }
        return Util::toFloat($this->reducao);
    }

    public function setReducao($reducao)
    {
        $this->reducao = $reducao;
        return $this;
    }

    public function toArray()
    {
        $parcial = parent::toArray();
        $parcial['modalidade'] = $this->getModalidade();
        $parcial['margem'] = $this->getMargem();
        $parcial['reducao'] = $this->getReducao();
        return $parcial;
    }

    public function fromArray($parcial = array())
    {
        if ($parcial instanceof Parcial) {
            $parcial = $parcial->toArray();
        } elseif (!is_array($parcial)) {
            return $this;
        }
        parent::fromArray($parcial);
        if (isset($parcial['modalidade'])) {
            $this->setModalidade($parcial['modalidade']);
        } else {
            $this->setModalidade(null);
        }
        if (isset($parcial['margem'])) {
            $this->setMargem($parcial['margem']);
        } else {
            $this->setMargem(null);
        }
        if (isset($parcial['reducao'])) {
            $this->setReducao($parcial['reducao']);
        } else {
            $this->setReducao(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'IMCS30':$name);
        $element->appendChild($dom->createElement('orig', $this->getOrigem(true)));
        $element->appendChild($dom->createElement('CST', $this->getTributacao(true)));
        $element->appendChild($dom->createElement('modBCST', $this->getModalidade(true)));
        $element->appendChild($dom->createElement('pMVAST', $this->getMargem(true)));
        $element->appendChild($dom->createElement('pRedBCST', $this->getReducao(true)));
        $element->appendChild($dom->createElement('vBCST', $this->getBase(true)));
        $element->appendChild($dom->createElement('pICMSST', $this->getAliquota(true)));
        $element->appendChild($dom->createElement('vICMSST', $this->getValor(true)));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IMCS30':$name;
        if ($element->tagName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new Exception('Tag "'.$name.'" do ICMS Parcial não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $_fields = $element->getElementsByTagName('orig');
        if ($_fields->length > 0) {
            $origem = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "orig" do campo "Origem" não encontrada no ICMS Parcial', 404);
        }
        $this->setOrigem($origem);
        $_fields = $element->getElementsByTagName('CST');
        if ($_fields->length > 0) {
            $tributacao = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "CST" do campo "Tributacao" não encontrada no ICMS Parcial', 404);
        }
        $this->setTributacao($tributacao);
        $_fields = $element->getElementsByTagName('modBCST');
        if ($_fields->length > 0) {
            $modalidade = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "modBCST" do campo "Modalidade" não encontrada no ICMS Parcial', 404);
        }
        $this->setModalidade($modalidade);
        $_fields = $element->getElementsByTagName('pMVAST');
        if ($_fields->length > 0) {
            $margem = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "pMVAST" do campo "Margem" não encontrada no ICMS Parcial', 404);
        }
        $this->setMargem($margem);
        $_fields = $element->getElementsByTagName('pRedBCST');
        if ($_fields->length > 0) {
            $reducao = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "pRedBCST" do campo "Reducao" não encontrada no ICMS Parcial', 404);
        }
        $this->setReducao($reducao);
        $_fields = $element->getElementsByTagName('vBCST');
        if ($_fields->length > 0) {
            $base = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "vBCST" do campo "Base" não encontrada no ICMS Parcial', 404);
        }
        $this->setBase($base);
        $_fields = $element->getElementsByTagName('pICMSST');
        if ($_fields->length > 0) {
            $aliquota = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "pICMSST" do campo "Aliquota" não encontrada no ICMS Parcial', 404);
        }
        $this->setAliquota($aliquota);
        return $element;
    }
}
