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
 * Empresa que irá emitir as notas fiscais
 */
class Emitente extends Pessoa
{

    /**
     * Código de Regime Tributário. Este campo será obrigatoriamente preenchido
     * com: 1 – Simples Nacional; 2 – Simples Nacional – excesso de sublimite
     * de receita bruta; 3 – Regime Normal.
     */
    const REGIME_SIMPLES = 'simples';
    const REGIME_EXCESSO = 'excesso';
    const REGIME_NORMAL = 'normal';

    private $fantasia;
    private $regime;

    public function __construct($emitente = array())
    {
        parent::__construct($emitente);
    }

    /**
     * Nome fantasia do da empresa emitente
     */
    public function getFantasia($normalize = false)
    {
        if (!$normalize) {
            return $this->fantasia;
        }
        return $this->fantasia;
    }

    public function setFantasia($fantasia)
    {
        $this->fantasia = $fantasia;
        return $this;
    }

    /**
     * Código de Regime Tributário. Este campo será obrigatoriamente preenchido
     * com: 1 – Simples Nacional; 2 – Simples Nacional – excesso de sublimite
     * de receita bruta; 3 – Regime Normal.
     */
    public function getRegime($normalize = false)
    {
        if (!$normalize) {
            return $this->regime;
        }
        switch ($this->regime) {
            case self::REGIME_SIMPLES:
                return '1';
            case self::REGIME_EXCESSO:
                return '2';
            case self::REGIME_NORMAL:
                return '3';
        }
        return $this->regime;
    }

    public function setRegime($regime)
    {
        $this->regime = $regime;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $emitente = parent::toArray($recursive);
        $emitente['fantasia'] = $this->getFantasia();
        $emitente['regime'] = $this->getRegime();
        return $emitente;
    }

    public function fromArray($emitente = array())
    {
        if ($emitente instanceof Emitente) {
            $emitente = $emitente->toArray();
        } elseif (!is_array($emitente)) {
            return $this;
        }
        parent::fromArray($emitente);
        if (is_null($this->getEndereco())) {
            $this->setEndereco(new Endereco());
        }
        if (isset($emitente['fantasia'])) {
            $this->setFantasia($emitente['fantasia']);
        } else {
            $this->setFantasia(null);
        }
        if (!isset($emitente['regime']) || is_null($emitente['regime'])) {
            $this->setRegime(self::REGIME_SIMPLES);
        } else {
            $this->setRegime($emitente['regime']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'emit':$name);
        Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        Util::appendNode($element, 'xNome', $this->getRazaoSocial(true));
        if (!is_null($this->getFantasia())) {
            Util::appendNode($element, 'xFant', $this->getFantasia(true));
        }
        $endereco = $this->getEndereco()->getNode('enderEmit');
        $endereco = $dom->importNode($endereco, true);
        if (!is_null($this->getTelefone())) {
            Util::appendNode($endereco, 'fone', $this->getTelefone(true));
        }
        $element->appendChild($endereco);
        Util::appendNode($element, 'IE', $this->getIE(true));
        if (!is_null($this->getIM())) {
            Util::appendNode($element, 'IM', $this->getIM(true));
        }
        Util::appendNode($element, 'CRT', $this->getRegime(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'emit':$name;
        $element = parent::loadNode($element, $name);
        $this->setFantasia(Util::loadNode($element, 'xFant'));
        $this->setRegime(
            Util::loadNode(
                $element,
                'CRT',
                'Tag "CRT" do campo "Regime" não encontrada'
            )
        );
        return $element;
    }
}
