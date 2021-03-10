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

use DOMElement;
use NFe\Common\Util;
use NFe\Common\Node;

/**
 * Informações do Intermediador da Transação
 */
class Intermediador implements Node
{
    /**
     * CNPJ do Intermediador da Transação (agenciador, plataforma de delivery,
     * marketplace e similar) de serviços e de negócios.
     *
     * @var string
     */
    private $cnpj;

    /**
     * Identificador cadastrado no intermediador
     *
     * @var string
     */
    private $identificador;

    /**
     * Constroi uma instância de Intermediador vazia
     * @param array $intermediador Array contendo dados do Intermediador
     */
    public function __construct($intermediador = [])
    {
        $this->fromArray($intermediador);
    }

    /**
     * CNPJ do Intermediador da Transação (agenciador, plataforma de delivery,
     * marketplace e similar) de serviços e de negócios.
     * @param boolean $normalize informa se o cnpj deve estar no formato do XML
     * @return string cnpj of Intermediador
     */
    public function getCNPJ($normalize = false)
    {
        if (!$normalize) {
            return $this->cnpj;
        }
        return $this->cnpj;
    }
    
    /**
     * Altera o valor do CNPJ para o informado no parâmetro
     * @param mixed $cnpj novo valor para CNPJ
     * @param string $cnpj Novo cnpj para Intermediador
     * @return self A própria instância da classe
     */
    public function setCNPJ($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Identificador cadastrado no intermediador
     * @param boolean $normalize informa se o identificador deve estar no formato do XML
     * @return string identificador of Intermediador
     */
    public function getIdentificador($normalize = false)
    {
        if (!$normalize) {
            return $this->identificador;
        }
        return $this->identificador;
    }
    
    /**
     * Altera o valor do Identificador para o informado no parâmetro
     * @param mixed $identificador novo valor para Identificador
     * @param string $identificador Novo identificador para Intermediador
     * @return self A própria instância da classe
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $intermediador = [];
        $intermediador['cnpj'] = $this->getCNPJ();
        $intermediador['identificador'] = $this->getIdentificador();
        return $intermediador;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $intermediador Array ou instância de Intermediador, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($intermediador = [])
    {
        if ($intermediador instanceof Intermediador) {
            $intermediador = $intermediador->toArray();
        } elseif (!is_array($intermediador)) {
            return $this;
        }
        if (!isset($intermediador['cnpj'])) {
            $this->setCNPJ(null);
        } else {
            $this->setCNPJ($intermediador['cnpj']);
        }
        if (!isset($intermediador['identificador'])) {
            $this->setIdentificador(null);
        } else {
            $this->setIdentificador($intermediador['identificador']);
        }
        return $this;
    }

    /**
     * Cria um nó XML do intermediador de acordo com o leiaute da NFe
     * @param string $name Nome do nó que será criado
     * @return DOMElement Nó que contém todos os campos da classe
     */
    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'infIntermed' : $name);
        Util::appendNode($element, 'cnpj', $this->getCNPJ(true));
        Util::appendNode($element, 'idCadIntTran', $this->getIdentificador(true));
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param DOMElement $element Nó do xml com todos as tags dos campos
     * @param string $name Nome do nó que será carregado
     * @return DOMElement Instância do nó que foi carregado
     */
    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'infIntermed' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception("Tag \"$name\" do Intermediador não encontrada", 404);
            }
            $element = $_fields->item(0);
        }
        $this->setCNPJ(
            Util::loadNode(
                $element,
                'cnpj',
                'Tag "cnpj" não encontrada no Intermediador'
            )
        );
        $this->setIdentificador(
            Util::loadNode(
                $element,
                'idCadIntTran',
                'Tag "idCadIntTran" não encontrada no Intermediador'
            )
        );
        return $element;
    }
}
