<?php
/**
 * MIT License
 *
 * Copyright (c) 2016 MZ Desenvolvimento de Sistemas LTDA
 *
 * @author  Francimar Alves <mazinsw@gmail.com>
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
use NFe\Common\Node;

/**
 * Dados dos totais da NF-e e do produto
 */
class Total implements Node
{

    /**
     * Valor Total dos produtos e serviços
     */
    private $produtos;
    /**
     * Valor do Desconto
     */
    private $desconto;
    /**
     * informar o valor do Seguro, o Seguro deve ser rateado entre os itens de
     * produto
     */
    private $seguro;
    /**
     * informar o valor do Frete, o Frete deve ser rateado entre os itens de
     * produto.
     */
    private $frete;
    /**
     * informar o valor de outras despesas acessórias do item de produto ou
     * serviço
     */
    private $despesas;
    /**
     * Valor estimado total de impostos federais, estaduais e municipais
     */
    private $tributos;
    /**
     * Informações complementares de interesse do Contribuinte
     */
    private $complemento;
    
    /**
     * Constroi uma instância de Total vazia
     * @param  array $total Array contendo dados do Total
     */
    public function __construct($total = array())
    {
        $this->fromArray($total);
    }

    /**
     * Valor Total dos produtos e serviços
     * @param boolean $normalize informa se o produtos deve estar no formato do XML
     * @return mixed produtos do Total
     */
    public function getProdutos($normalize = false)
    {
        if (!$normalize) {
            return $this->produtos;
        }
        return Util::toCurrency($this->produtos);
    }
    
    /**
     * Altera o valor do Produtos para o informado no parâmetro
     * @param mixed $produtos novo valor para Produtos
     * @return Total A própria instância da classe
     */
    public function setProdutos($produtos)
    {
        if (trim($produtos) != '') {
            $produtos = floatval($produtos);
        }
        $this->produtos = $produtos;
        return $this;
    }

    /**
     * Valor do Desconto
     * @param boolean $normalize informa se o desconto deve estar no formato do XML
     * @return mixed desconto do Total
     */
    public function getDesconto($normalize = false)
    {
        if (!$normalize) {
            return $this->desconto;
        }
        return Util::toCurrency($this->desconto);
    }
    
    /**
     * Altera o valor do Desconto para o informado no parâmetro
     * @param mixed $desconto novo valor para Desconto
     * @return Total A própria instância da classe
     */
    public function setDesconto($desconto)
    {
        if (trim($desconto) != '') {
            $desconto = floatval($desconto);
        }
        $this->desconto = $desconto;
        return $this;
    }

    /**
     * informar o valor do Seguro, o Seguro deve ser rateado entre os itens de
     * produto
     * @param boolean $normalize informa se o seguro deve estar no formato do XML
     * @return mixed seguro do Total
     */
    public function getSeguro($normalize = false)
    {
        if (!$normalize) {
            return $this->seguro;
        }
        return Util::toCurrency($this->seguro);
    }
    
    /**
     * Altera o valor do Seguro para o informado no parâmetro
     * @param mixed $seguro novo valor para Seguro
     * @return Total A própria instância da classe
     */
    public function setSeguro($seguro)
    {
        if (trim($seguro) != '') {
            $seguro = floatval($seguro);
        }
        $this->seguro = $seguro;
        return $this;
    }

    /**
     * informar o valor do Frete, o Frete deve ser rateado entre os itens de
     * produto.
     * @param boolean $normalize informa se o frete deve estar no formato do XML
     * @return mixed frete do Total
     */
    public function getFrete($normalize = false)
    {
        if (!$normalize) {
            return $this->frete;
        }
        return Util::toCurrency($this->frete);
    }
    
    /**
     * Altera o valor do Frete para o informado no parâmetro
     * @param mixed $frete novo valor para Frete
     * @return Total A própria instância da classe
     */
    public function setFrete($frete)
    {
        if (trim($frete) != '') {
            $frete = floatval($frete);
        }
        $this->frete = $frete;
        return $this;
    }

    /**
     * informar o valor de outras despesas acessórias do item de produto ou
     * serviço
     * @param boolean $normalize informa se a despesas deve estar no formato do XML
     * @return mixed despesas do Total
     */
    public function getDespesas($normalize = false)
    {
        if (!$normalize) {
            return $this->despesas;
        }
        return Util::toCurrency($this->despesas);
    }
    
    /**
     * Altera o valor da Despesas para o informado no parâmetro
     * @param mixed $despesas novo valor para Despesas
     * @return Total A própria instância da classe
     */
    public function setDespesas($despesas)
    {
        if (trim($despesas) != '') {
            $despesas = floatval($despesas);
        }
        $this->despesas = $despesas;
        return $this;
    }

    /**
     * Valor estimado total de impostos federais, estaduais e municipais
     * @param boolean $normalize informa se o tributos deve estar no formato do XML
     * @return mixed tributos do Total
     */
    public function getTributos($normalize = false)
    {
        if (!$normalize) {
            return $this->tributos;
        }
        return Util::toCurrency($this->tributos);
    }
    
    /**
     * Altera o valor do Tributos para o informado no parâmetro
     * @param mixed $tributos novo valor para Tributos
     * @return Total A própria instância da classe
     */
    public function setTributos($tributos)
    {
        if (trim($tributos) != '') {
            $tributos = floatval($tributos);
        }
        $this->tributos = $tributos;
        return $this;
    }

    /**
     * Informações complementares de interesse do Contribuinte
     * @param boolean $normalize informa se o complemento deve estar no formato do XML
     * @return mixed complemento do Total
     */
    public function getComplemento($normalize = false)
    {
        if (!$normalize) {
            return $this->complemento;
        }
        return $this->complemento;
    }
    
    /**
     * Altera o valor do Complemento para o informado no parâmetro
     * @param mixed $complemento novo valor para Complemento
     * @return Total A própria instância da classe
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $total = array();
        $total['produtos'] = $this->getProdutos();
        $total['desconto'] = $this->getDesconto();
        $total['seguro'] = $this->getSeguro();
        $total['frete'] = $this->getFrete();
        $total['despesas'] = $this->getDespesas();
        $total['tributos'] = $this->getTributos();
        $total['complemento'] = $this->getComplemento();
        return $total;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $total Array ou instância de Total, para copiar os valores
     * @return Total A própria instância da classe
     */
    public function fromArray($total = array())
    {
        if ($total instanceof Total) {
            $total = $total->toArray();
        } elseif (!is_array($total)) {
            return $this;
        }
        if (!isset($total['produtos'])) {
            $this->setProdutos(null);
        } else {
            $this->setProdutos($total['produtos']);
        }
        if (!array_key_exists('desconto', $total)) {
            $this->setDesconto(null);
        } else {
            $this->setDesconto($total['desconto']);
        }
        if (!array_key_exists('seguro', $total)) {
            $this->setSeguro(null);
        } else {
            $this->setSeguro($total['seguro']);
        }
        if (!array_key_exists('frete', $total)) {
            $this->setFrete(null);
        } else {
            $this->setFrete($total['frete']);
        }
        if (!array_key_exists('despesas', $total)) {
            $this->setDespesas(null);
        } else {
            $this->setDespesas($total['despesas']);
        }
        if (!array_key_exists('tributos', $total)) {
            $this->setTributos(null);
        } else {
            $this->setTributos($total['tributos']);
        }
        if (!array_key_exists('complemento', $total)) {
            $this->setComplemento(null);
        } else {
            $this->setComplemento($total['complemento']);
        }
        return $this;
    }

    /**
     * Cria um nó XML do total de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'prod':$name);
        Util::appendNode($element, 'vProd', $this->getProdutos(true));
        if (!is_null($this->getDesconto())) {
            Util::appendNode($element, 'vDesc', $this->getDesconto(true));
        }
        if (!is_null($this->getSeguro())) {
            Util::appendNode($element, 'vSeg', $this->getSeguro(true));
        }
        if (!is_null($this->getFrete())) {
            Util::appendNode($element, 'vFrete', $this->getFrete(true));
        }
        if (!is_null($this->getDespesas())) {
            Util::appendNode($element, 'vOutro', $this->getDespesas(true));
        }
        if (!is_null($this->getTributos())) {
            Util::appendNode($element, 'vTotTrib', $this->getTributos(true));
        }
        if (!is_null($this->getComplemento())) {
            Util::appendNode($element, 'infCpl', $this->getComplemento(true));
        }
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'prod':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" do Total ou Produto não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setProdutos(
            Util::loadNode(
                $element,
                'vProd',
                'Tag "vProd" não encontrada no Total ou Produto'
            )
        );
        $this->setDesconto(Util::loadNode($element, 'vDesc'));
        $this->setSeguro(Util::loadNode($element, 'vSeg'));
        $this->setFrete(Util::loadNode($element, 'vFrete'));
        $this->setDespesas(Util::loadNode($element, 'vOutro'));
        $this->setTributos(Util::loadNode($element, 'vTotTrib'));
        $this->setComplemento(Util::loadNode($element, 'infCpl'));
        return $element;
    }
}
