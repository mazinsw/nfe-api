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
 * Tributação pelo ICMS
 * 40 - Isenta
 * 41 - Não tributada
 * 50 - Suspensão,
 * estende de Generico
 */
class Isento extends Generico
{

    /**
     * Informar o motivo da desoneração:
     * 1 – Táxi;
     * 3 – Produtor Agropecuário;
     * 4
     * – Frotista/Locadora;
     * 5 – Diplomático/Consular;
     * 6 – Utilitários e
     * Motocicletas da Amazônia Ocidental e Áreas de Livre Comércio (Resolução
     * 714/88 e 790/94 – CONTRAN e suas alterações);
     * 7 – SUFRAMA;
     * 8 - Venda a
     * órgão Público;
     * 9 – Outros
     * 10- Deficiente Condutor
     * 11- Deficiente não
     * condutor
     * 16 - Olimpíadas Rio 2016
     */
    const MOTIVO_TAXI = 'taxi';
    const MOTIVO_PRODUTOR = 'produtor';
    const MOTIVO_LOCADORA = 'locadora';
    const MOTIVO_CONSULAR = 'consular';
    const MOTIVO_CONTRAN = 'contran';
    const MOTIVO_SUFRAMA = 'suframa';
    const MOTIVO_VENDA = 'venda';
    const MOTIVO_OUTROS = 'outros';
    const MOTIVO_CONDUTOR = 'condutor';
    const MOTIVO_DEFICIENTE = 'deficiente';
    const MOTIVO_OLIMPIADAS = 'olimpiadas';

    private $desoneracao;
    private $motivo;

    public function __construct($isento = array())
    {
        parent::__construct($isento);
        $this->setTributacao('40');
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return 0.00; // sempre zero
        }
        return Util::toCurrency($this->getBase());
    }

    /**
     * O valor do ICMS será informado apenas nas operações com veículos
     * beneficiados com a desoneração condicional do ICMS.
     */
    public function getDesoneracao($normalize = false)
    {
        if (!$normalize) {
            return $this->desoneracao;
        }
        return Util::toCurrency($this->desoneracao);
    }

    public function setDesoneracao($desoneracao)
    {
        if (trim($desoneracao) != '') {
            $desoneracao = floatval($desoneracao);
        }
        $this->desoneracao = $desoneracao;
        return $this;
    }

    /**
     * Informar o motivo da desoneração:
     * 1 – Táxi;
     * 3 – Produtor Agropecuário;
     * 4
     * – Frotista/Locadora;
     * 5 – Diplomático/Consular;
     * 6 – Utilitários e
     * Motocicletas da Amazônia Ocidental e Áreas de Livre Comércio (Resolução
     * 714/88 e 790/94 – CONTRAN e suas alterações);
     * 7 – SUFRAMA;
     * 8 - Venda a
     * órgão Público;
     * 9 – Outros
     * 10- Deficiente Condutor
     * 11- Deficiente não
     * condutor
     * 16 - Olimpíadas Rio 2016
     */
    public function getMotivo($normalize = false)
    {
        if (!$normalize) {
            return $this->motivo;
        }
        switch ($this->motivo) {
            case self::MOTIVO_TAXI:
                return '1';
            case self::MOTIVO_PRODUTOR:
                return '3';
            case self::MOTIVO_LOCADORA:
                return '4';
            case self::MOTIVO_CONSULAR:
                return '5';
            case self::MOTIVO_CONTRAN:
                return '6';
            case self::MOTIVO_SUFRAMA:
                return '7';
            case self::MOTIVO_VENDA:
                return '8';
            case self::MOTIVO_OUTROS:
                return '9';
            case self::MOTIVO_CONDUTOR:
                return '10';
            case self::MOTIVO_DEFICIENTE:
                return '11';
            case self::MOTIVO_OLIMPIADAS:
                return '16';
        }
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $isento = parent::toArray($recursive);
        $isento['desoneracao'] = $this->getDesoneracao();
        $isento['motivo'] = $this->getMotivo();
        return $isento;
    }

    public function fromArray($isento = array())
    {
        if ($isento instanceof Isento) {
            $isento = $isento->toArray();
        } elseif (!is_array($isento)) {
            return $this;
        }
        parent::fromArray($isento);
        if (isset($isento['desoneracao'])) {
            $this->setDesoneracao($isento['desoneracao']);
        } else {
            $this->setDesoneracao(null);
        }
        if (isset($isento['motivo'])) {
            $this->setMotivo($isento['motivo']);
        } else {
            $this->setMotivo(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name)?'ICMS40':$name);
        $dom = $element->ownerDocument;
        if (!is_null($this->getDesoneracao())) {
            Util::appendNode($element, 'vICMSDeson', $this->getDesoneracao(true));
        }
        if (!is_null($this->getDesoneracao())) {
            Util::appendNode($element, 'motDesICMS', $this->getMotivo(true));
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMS40':$name;
        $element = parent::loadNode($element, $name);
        $this->setDesoneracao(Util::loadNode($element, 'vICMSDeson'));
        $this->setMotivo(Util::loadNode($element, 'motDesICMS'));
        return $element;
    }
}
