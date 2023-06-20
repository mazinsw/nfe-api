<?php

/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
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

use NFe\Common\Node;
use NFe\Common\Util;

class Cobranca implements Node
{
    private $num_fatura;
    private $valor_fatura;
    private $desconto;
    private $valor_liquido;
    private $num_duplicata;
    private $vencimento;
    private $valor_duplicata;

    /**
     * Numero da Fatura
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return string|string valor of cobranca
     */
    public function getNumFatura($normalize = false)
    {
        if (!$normalize) {
            return $this->num_fatura;
        }
        return $this->num_duplicata;
    }

    /**
     * Altera o numero da fatura para o informado no parâmetro
     *
     * @param string|string|null $num_fatura Novo Numero de Fatura para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setNumFatura($num_fatura)
    {
        $this->num_fatura = $num_fatura;
        return $this;
    }

    /**
     * Valor do cobranca
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float|string valor of cobranca
     */
    public function getValorFatura($normalize = false)
    {
        if (!$normalize) {
            return $this->valor_fatura;
        }
        return Util::toCurrency($this->valor_fatura);
    }

    /**
     * Altera o valor da Fatura para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor de Fatura para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setValorFatura($valor_fatura)
    {
        $valor_fatura = floatval($valor_fatura);
        $this->valor_fatura = $valor_fatura;
        return $this;
    }

    /**
     * Valor de desconto cobranca
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float|string valor of cobranca
     */
    public function getDesconto($normalize = false)
    {
        if (!$normalize) {
            return $this->desconto;
        }
        return Util::toCurrency($this->desconto);
    }

    /**
     * Altera o valor do desconto da Fatura para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor de desconto para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setDesconto($desconto)
    {
        $desconto = floatval($desconto);
        $this->desconto = $desconto;
        return $this;
    }

    /**
     * Valor liquido do cobranca
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float|string valor of cobranca
     */
    public function getValorLiquido($normalize = false)
    {
        if (!$normalize) {
            return $this->valor_liquido;
        }
        return Util::toCurrency($this->valor_liquido);
    }

    /**
     * Altera o valor do liquido da Fatura para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor liquido para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setValorLiquido($valor_liquido)
    {
        $valor_liquido = floatval($valor_liquido);
        $this->valor_liquido = $valor_liquido;
        return $this;
    }

    /**
     * Numero da Duplicata
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return string|string valor of cobranca
     */
    public function getNumDuplicata($normalize = false)
    {
        if (!$normalize) {
            return $this->num_duplicata;
        }
        return $this->num_duplicata;
    }

    /**
     * Altera o numero da duplicata para o informado no parâmetro
     *
     * @param string|string|null $num_duplicata Novo Numero de Duplicata para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setNumDuplicata($num_duplicata)
    {
        $this->num_duplicata = $num_duplicata;
        return $this;
    }

    /**
     * Vencimento do cobranca
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float|string valor of cobranca
     */
    public function getVencimento($normalize = false)
    {
        if (!$normalize) {
            return $this->vencimento;
        }
        return $this->vencimento;
    }

    /**
     * Altera a data de vencimento da duplicata para o informado no parâmetro
     *
     * @param float|string|null $valor Novo vencimento da duplicata para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
        return $this;
    }

    /**
     * Valor de duplicata para cobranca
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float|string valor of cobranca
     */
    public function getValorDuplicata($normalize = false)
    {
        if (!$normalize) {
            return $this->valor_duplicata;
        }
        return Util::toCurrency($this->valor_duplicata);
    }

    /**
     * Altera o valor da duplicata para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor de duplicata para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setValorDuplicata($valor_duplicata)
    {
        $valor_duplicata = floatval($valor_duplicata);
        $this->valor_duplicata = $valor_duplicata;
        return $this;
    }
       
    /**
     * Constroi uma instância de cobranca vazia
     * @param array $cobranca Array contendo dados do cobranca
     */
    public function __construct($cobranca = [])
    {
        $this->fromArray($cobranca);
    }

    public function toArray($recursive = false)
    {
        $cobranca = [];
        $cobranca['num_fatura'] = $this->getNumFatura();
        $cobranca['valor_fatura'] = $this->getValorFatura();
        $cobranca['desconto'] = $this->getDesconto();
        $cobranca['valor_liquido'] = $this->getValorLiquido();
        $cobranca['num_duplicata'] = $this->getNumDuplicata();
        $cobranca['vencimento'] = $this->getVencimento();
        $cobranca['valor_duplicata'] = $this->getValorDuplicata();
        return $cobranca;
    }

    public function fromArray($cobranca = [])
    {
        if ($cobranca instanceof cobranca) {
            $cobranca = $cobranca->toArray();
        } elseif (!is_array($cobranca)) {
            return $this;
        }

        if (isset($cobranca['num_fatura'])) {
            $this->setNumFatura($cobranca['num_fatura']);
        } else {
            $this->setNumFatura(null);
        }

        if (isset($cobranca['valor_fatura'])) {
            $this->setValorFatura($cobranca['valor_fatura']);
        } else {
            $this->setValorFatura(null);
        }

        if (isset($cobranca['desconto'])) {
            $this->setDesconto($cobranca['desconto']);
        } else {
            $this->setDesconto(null);
        }

        if (isset($cobranca['valor_liquido'])) {
            $this->setValorLiquido($cobranca['valor_liquido']);
        } else {
            $this->setValorLiquido(null);
        }

        if (isset($cobranca['num_duplicata'])) {
            $this->setNumDuplicata($cobranca['num_duplicata']);
        } else {
            $this->setNumDuplicata(null);
        }
        if (isset($cobranca['vencimento'])) {
            $this->setVencimento($cobranca['vencimento']);
        } else {
            $this->setVencimento(null);
        }
        if (isset($cobranca['valor_duplicata'])) {
            $this->setValorDuplicata($cobranca['valor_duplicata']);
        } else {
            $this->setValorDuplicata(null);
        }
        return $this;
    }

    
    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        /*if ($this->getValor() < 0) {
            $element = $dom->createElement(is_null($name) ? 'vTroco' : $name);
            $this->setValor(-floatval($this->getValor()));
            $element->appendChild($dom->createTextNode($this->getValor(true)));
            $this->setValor(-floatval($this->getValor()));
            return $element;
        }
        $element = $dom->createElement(is_null($name) ? 'detPag' : $name);
        if (!is_null($this->getIndicador())) {
            Util::appendNode($element, 'indPag', $this->getIndicador(true));
        }
        Util::appendNode($element, 'tPag', $this->getForma(true));
        Util::appendNode($element, 'vPag', $this->getValor(true));
        if (!$this->isCartao()) {
            return $element;
        }
        $cartao = $dom->createElement('card');
        Util::appendNode($cartao, 'tpIntegra', $this->getIntegrado(true));
        if ($this->isIntegrado()) {
            Util::appendNode($cartao, 'CNPJ', $this->getCredenciadora(true));
        }
        if (!is_null($this->getBandeira())) {
            Util::appendNode($cartao, 'tBand', $this->getBandeira(true));
        }
        if ($this->isIntegrado()) {
            Util::appendNode($cartao, 'cAut', $this->getAutorizacao(true));
        }
        $element->appendChild($cartao);
        return $element;*/
        return null;
    }
    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'dup' : $name;
        
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setNumFatura(
            Util::loadNode(
                $element,
                'nFat'
            )
        );
        $this->setValorFatura(
            Util::loadNode(
                $element,
                'vOrig'
            )
        );
        $this->setDesconto(
            Util::loadNode(
                $element,
                'vDesc'
            )
        );
        $this->setValorLiquido(
            Util::loadNode(
                $element,
                'vLiq'
            )
        );
        $this->setNumDuplicata(
            Util::loadNode(
                $element,
                'nDup'
            )
        );
        $this->setVencimento(
            Util::loadNode(
                $element,
                'dVenc'
            )
        );
        $this->setValorDuplicata(
            Util::loadNode(
                $element,
                'vDup'
            )
        );
        return $element;
    }
}
