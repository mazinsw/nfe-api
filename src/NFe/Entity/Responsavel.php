<?php

/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
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

use DOMElement;
use NFe\Common\Util;
use NFe\Common\Node;

/**
 * Grupo de informações do responsável técnico pelo sistema
 */
class Responsavel extends Pessoa implements Node
{
    /**
     * Informar o nome da pessoa a ser contatada na empresa desenvolvedora do
     * sistema utilizado na emissão do documento fiscal eletrônico.
     */
    private $contato;
    private $email;

    /**
     * Identificador do CSRT utilizado para montar o hash do CSRT
     */
    private $idcsrt;

    /**
     * O hashCSRT é o resultado da função hash (SHA-1 – Base64) do CSRT
     * fornecido pelo fisco mais a Chave de Acesso da NFe.
     */
    private $hash_csrt;

    /**
     * Constroi uma instância de Responsavel vazia
     * @param  array $responsavel Array contendo dados do Responsavel
     */
    public function __construct($responsavel = [])
    {
        $this->fromArray($responsavel);
    }

    /**
     * Informar o nome da pessoa a ser contatada na empresa desenvolvedora do
     * sistema utilizado na emissão do documento fiscal eletrônico.
     * @param boolean $normalize informa se o contato deve estar no formato do XML
     * @return mixed contato do Responsavel
     */
    public function getContato($normalize = false)
    {
        if (!$normalize) {
            return $this->contato;
        }
        return $this->contato;
    }

    /**
     * Altera o valor do Contato para o informado no parâmetro
     * @param mixed $contato novo valor para Contato
     * @return self A própria instância da classe
     */
    public function setContato($contato)
    {
        $this->contato = $contato;
        return $this;
    }

    public function getEmail($normalize = false)
    {
        if (!$normalize) {
            return $this->email;
        }
        return $this->email;
    }

    /**
     * Altera o valor da Email para o informado no parâmetro
     * @param mixed $email novo valor para Email
     * @return self A própria instância da classe
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Identificador do CSRT utilizado para montar o hash do CSRT
     * @param boolean $normalize informa se a id_csrt deve estar no formato do XML
     * @return mixed id_csrt do Responsavel
     */
    public function getIDCsrt($normalize = false)
    {
        if (!$normalize) {
            return $this->idcsrt;
        }
        return $this->idcsrt;
    }

    /**
     * Altera o valor da IDCsrt para o informado no parâmetro
     * @param mixed $idcsrt novo valor para IDCsrt
     * @return self A própria instância da classe
     */
    public function setIDCsrt($idcsrt)
    {
        if (trim($idcsrt ?: '') != '') {
            $idcsrt = intval($idcsrt);
        }
        $this->idcsrt = $idcsrt;
        return $this;
    }

    /**
     * O hashCSRT é o resultado da função hash (SHA-1 – Base64) do CSRT
     * fornecido pelo fisco mais a Chave de Acesso da NFe.
     * @param boolean $normalize informa se a hash_csrt deve estar no formato do XML
     * @return mixed hash_csrt do Responsavel
     */
    public function getHashCsrt($normalize = false)
    {
        if (!$normalize) {
            return $this->hash_csrt;
        }
        return $this->hash_csrt;
    }

    /**
     * Altera o valor da HashCsrt para o informado no parâmetro
     * @param mixed $hash_csrt novo valor para HashCsrt
     * @return self A própria instância da classe
     */
    public function setHashCsrt($hash_csrt)
    {
        $this->hash_csrt = $hash_csrt;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $responsavel = [];
        $responsavel['cnpj'] = $this->getCNPJ();
        $responsavel['contato'] = $this->getContato();
        $responsavel['email'] = $this->getEmail();
        $responsavel['telefone'] = $this->getTelefone();
        $responsavel['id_csrt'] = $this->getIDCsrt();
        $responsavel['hash_csrt'] = $this->getHashCsrt();
        return $responsavel;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $responsavel Array ou instância de Responsavel, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($responsavel = [])
    {
        if ($responsavel instanceof Responsavel) {
            $responsavel = $responsavel->toArray();
        } elseif (!is_array($responsavel)) {
            return $this;
        }
        if (!isset($responsavel['cnpj'])) {
            $this->setCNPJ(null);
        } else {
            $this->setCNPJ($responsavel['cnpj']);
        }
        if (!isset($responsavel['contato'])) {
            $this->setContato(null);
        } else {
            $this->setContato($responsavel['contato']);
        }
        if (!isset($responsavel['email'])) {
            $this->setEmail(null);
        } else {
            $this->setEmail($responsavel['email']);
        }
        if (!isset($responsavel['telefone'])) {
            $this->setTelefone(null);
        } else {
            $this->setTelefone($responsavel['telefone']);
        }
        if (!array_key_exists('id_csrt', $responsavel)) {
            $this->setIDCsrt(null);
        } else {
            $this->setIDCsrt($responsavel['id_csrt']);
        }
        if (!array_key_exists('hash_csrt', $responsavel)) {
            $this->setHashCsrt(null);
        } else {
            $this->setHashCsrt($responsavel['hash_csrt']);
        }
        return $this;
    }

    /**
     * Cria um nó XML do responsavel de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'infRespTec' : $name);
        Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        Util::appendNode($element, 'xContato', $this->getContato(true));
        Util::appendNode($element, 'email', $this->getEmail(true));
        Util::appendNode($element, 'fone', $this->getTelefone(true));
        if (!is_null($this->getIDCsrt())) {
            Util::appendNode($element, 'idCSRT', $this->getIDCsrt(true));
        }
        if (!is_null($this->getHashCsrt())) {
            Util::appendNode($element, 'hashCSRT', $this->getHashCsrt(true));
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
        $name = is_null($name) ? 'infRespTec' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" do Responsavel não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setCNPJ(Util::loadNode($element, 'CNPJ', 'Tag "CNPJ" não encontrada no Responsavel'));
        $this->setContato(Util::loadNode($element, 'xContato', 'Tag "xContato" não encontrada no Responsavel'));
        $this->setEmail(Util::loadNode($element, 'email', 'Tag "email" não encontrada no Responsavel'));
        $this->setTelefone(Util::loadNode($element, 'fone', 'Tag "fone" não encontrada no Responsavel'));
        $this->setIDCsrt(Util::loadNode($element, 'idCSRT'));
        $this->setHashCsrt(Util::loadNode($element, 'hashCSRT'));
        return $element;
    }
}
