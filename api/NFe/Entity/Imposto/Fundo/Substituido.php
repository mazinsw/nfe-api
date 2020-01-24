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
namespace NFe\Entity\Imposto\Fundo;

use NFe\Common\Util;

/**
 * Valor e Percentual do imposto para o Fundo de Combate à Pobreza retido
 * por substituição tributária
 */
class Substituido extends Base
{

    
    /**
     * Constroi uma instância de Substituido vazia
     * @param  array $substituido Array contendo dados do Substituido
     */
    public function __construct($substituido = [])
    {
        parent::__construct($substituido);
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $substituido = parent::toArray($recursive);
        return $substituido;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $substituido Array ou instância de Substituido, para copiar os valores
     * @return Substituido A própria instância da classe
     */
    public function fromArray($substituido = [])
    {
        if ($substituido instanceof Substituido) {
            $substituido = $substituido->toArray();
        } elseif (!is_array($substituido)) {
            return $this;
        }
        parent::fromArray($substituido);
        $this->setGrupo(self::GRUPO_FCPST);
        return $this;
    }

    /**
     * Verifica se o elemento informado contém os dados dessa instância
     * @param DOMElement $element Nó que pode contér os dados dessa instância
     * @return boolean   True se contém os dados dessa instância ou false caso contrário
     */
    public function exists($element)
    {
        // se o primeiro campo obrigatório existir, significa que deve ter os outros campos
        return Util::nodeExists(
            $element,
            'vBCFCPST'
        );
    }

    /**
     * Cria um nó XML do substituido de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'FCPST' : $name);
        Util::appendNode($element, 'vBCFCPST', $this->getBase(true));
        Util::appendNode($element, 'pFCPST', $this->getAliquota(true));
        Util::appendNode($element, 'vFCPST', $this->getValor(true));
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
        $name = is_null($name) ? 'FCPST' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCFCPST',
                'Tag "vBCFCPST" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pFCPST',
                'Tag "pFCPST" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
