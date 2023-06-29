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
    /**
     * Tipos de cobrança 0 - fatura; 1 - duplicata
     */
    public const TIPO_FATURA  = 'fatura';
    public const TIPO_DUPLICATA = 'duplicata';

    /**
     * Numero da cobrança
     *
     * @var string
     */
    private $numero;

    /**
     * Valor total da cobrança
     *
     * @var float
     */
    private $valor;

    /**
     * valor do desconto da cobrança
     *
     * @var float
     */
    private $desconto;

    /**
     * Valor liquido da cobrança
     *
     * @var float
     */
    private $valor_liquido;

    /**
     * Data de vencimento da cobrança
     */
    private $vencimento;

    /**
     * Tipo da cobrança
     */
    private $tipo;

    /**
     * Numero da cobrança
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return string|string valor of cobranca
     */
    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    /**
     * Altera o numero da cobrança para o informado no parâmetro
     *
     * @param string|string|null $numero Novo Numero de Fatura para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Valor da cobranca
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float|string valor of cobranca
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->valor;
        }
        return Util::toCurrency($this->valor);
    }

    /**
     * Altera o valor da Cobrança para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor de Fatura para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setValor($valor)
    {
        $valor = floatval($valor);
        $this->valor = $valor;
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
     * @param float|string|null $desconto Novo valor de desconto para cobranca
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
     * @param float|string|null $valor_liquido Novo valor liquido para cobranca
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
     * @param float|string|null $vencimento Novo vencimento da duplicata para cobranca
     *
     * @return self A própria instância da classe
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
        return $this;
    }

    /**
     * Tipo da cobrança 0 – fatura; 1 – duplicata
     *
     * @param boolean $normalize informa se o indicador deve estar no formato do XML
     * @return mixed tipo da cobrança
     */
    public function getTipo($normalize = false)
    {
        if (!$normalize) {
            return $this->tipo;
        }
        switch ($this->tipo) {
            case self::TIPO_FATURA:
                return '0';
            case self::TIPO_DUPLICATA:
                return '1';
        }
        return $this->tipo;
    }

    /**
     * Altera o valor do tipo para o informado no parâmetro
     * @param mixed $tipo novo valor para tipo
     * @return self A própria instância da classe
     */
    public function setTipo($tipo)
    {
        switch ($tipo) {
            case '0':
                $tipo = self::TIPO_FATURA;
                break;
            case '1':
                $tipo = self::TIPO_DUPLICATA;
                break;
        }
        $this->tipo = $tipo;
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
        $cobranca['tipo'] = $this->getTipo();
        $cobranca['numero'] = $this->getNumero();
        $cobranca['valor'] = $this->getValor();
        $cobranca['desconto'] = $this->getDesconto();
        $cobranca['valor_liquido'] = $this->getValorLiquido();
        $cobranca['vencimento'] = $this->getVencimento();
        return $cobranca;
    }

    public function fromArray($cobranca = [])
    {
        if ($cobranca instanceof Cobranca) {
            $cobranca = $cobranca->toArray();
        } elseif (!is_array($cobranca)) {
            return $this;
        }
        if (isset($cobranca['tipo'])) {
            $this->setTipo($cobranca['tipo']);
        } else {
            $this->setTipo(null);
        }
        if (isset($cobranca['numero'])) {
            $this->setNumero($cobranca['numero']);
        } else {
            $this->setNumero(null);
        }
        if (isset($cobranca['valor'])) {
            $this->setValor($cobranca['valor']);
        } else {
            $this->setValor(null);
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
        if (isset($cobranca['vencimento'])) {
            $this->setVencimento($cobranca['vencimento']);
        } else {
            $this->setVencimento(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        // TODO: implementar a inserção da cobranca no xml
        throw new \Exception('NÃO IMPLEMENTADO', 404);
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
        $this->setTipo(
            $name == 'fat' ? 0 : 1
        );
        if ($this->getTipo() == self::TIPO_FATURA) {
            $this->setNumero(
                Util::loadNode(
                    $element,
                    'nFat'
                )
            );
            $this->setValor(
                Util::loadNode(
                    $element,
                    'vOrig'
                )
            );
        } else {
            $this->setNumero(
                Util::loadNode(
                    $element,
                    'nDup'
                )
            );
            $this->setValor(
                Util::loadNode(
                    $element,
                    'vDup'
                )
            );
        }
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
        $this->setVencimento(
            Util::loadNode(
                $element,
                'dVenc'
            )
        );
        return $element;
    }
}
