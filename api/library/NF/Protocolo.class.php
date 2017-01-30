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
namespace NF;

use NF;

class Protocolo extends Retorno
{

    private $chave;
    private $validacao;
    private $numero;

    public function __construct($protocolo = array())
    {
        parent::__construct($protocolo);
    }

    public function getChave($normalize = false)
    {
        if (!$normalize) {
            return $this->chave;
        }
        return $this->chave;
    }

    public function setChave($chave)
    {
        $this->chave = $chave;
        return $this;
    }

    public function getValidacao($normalize = false)
    {
        if (!$normalize) {
            return $this->validacao;
        }
        return $this->validacao;
    }

    public function setValidacao($validacao)
    {
        $this->validacao = $validacao;
        return $this;
    }

    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    public function toArray()
    {
        $protocolo = parent::toArray();
        $protocolo['chave'] = $this->getChave();
        $protocolo['validacao'] = $this->getValidacao();
        $protocolo['numero'] = $this->getNumero();
        return $protocolo;
    }

    public function fromArray($protocolo = array())
    {
        if ($protocolo instanceof Protocolo) {
            $protocolo = $protocolo->toArray();
        } elseif (!is_array($protocolo)) {
            return $this;
        }
        parent::fromArray($protocolo);
        if (isset($protocolo['chave'])) {
            $this->setChave($protocolo['chave']);
        } else {
            $this->setChave(null);
        }
        if (isset($protocolo['validacao'])) {
            $this->setValidacao($protocolo['validacao']);
        } else {
            $this->setValidacao(null);
        }
        if (isset($protocolo['numero'])) {
            $this->setNumero($protocolo['numero']);
        } else {
            $this->setNumero(null);
        }
        return $this;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'infProt':$name;
        $info = parent::loadNode($element, $name);
        $this->setChave($info->getElementsByTagName('chNFe')->item(0)->nodeValue);
        $_fields = $info->getElementsByTagName('digVal');
        $validacao = null;
        if ($_fields->length > 0) {
            $validacao = $_fields->item(0)->nodeValue;
        }
        $this->setValidacao($validacao);
        $_fields = $info->getElementsByTagName('nProt');
        $numero = null;
        if ($_fields->length > 0) {
            $numero = $_fields->item(0)->nodeValue;
        }
        $this->setNumero($numero);
        return $info;
    }

    public function getNode($name = null)
    {
        $old_uf = $this->getUF();
        $this->setUF(null);
        $info = parent::getNode('infProt');
        $this->setUF($old_uf);
        $dom = $info->ownerDocument;
        $element = $dom->createElement(is_null($name)?'protNFe':$name);
        $versao = $dom->createAttribute('versao');
        $versao->value = NF::VERSAO;
        $element->appendChild($versao);

        $id = $dom->createAttribute('Id');
        $id->value = 'ID'.$this->getNumero(true);
        $info->appendChild($id);

        $status = $info->getElementsByTagName('cStat')->item(0);
        $info->insertBefore($dom->createElement('nProt', $this->getNumero(true)), $status);
        $info->insertBefore($dom->createElement('digVal', $this->getValidacao(true)), $status);
        $nodes = $info->getElementsByTagName('dhRecbto');
        if ($nodes->length > 0) {
            $recebimento = $nodes->item(0);
        } else {
            $recebimento = $status;
        }
        $info->insertBefore($dom->createElement('chNFe', $this->getChave(true)), $recebimento);
        $element->appendChild($info);
        return $element;
    }
}
