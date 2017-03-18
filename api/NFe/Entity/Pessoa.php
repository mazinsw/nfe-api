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

use NFe\Common\Node;
use NFe\Common\Util;

/**
 * Classe base para preenchimento de informações de pessoas físicas e
 * empresas
 */
abstract class Pessoa implements Node
{

    private $razao_social;
    private $cnpj;
    private $ie;
    private $im;
    private $endereco;
    private $telefone;

    public function __construct($pessoa = array())
    {
        $this->fromArray($pessoa);
    }

    /**
     * Número identificador da pessoa
     */
    public function getID($normalize = false)
    {
        return $this->getCNPJ($normalize);
    }

    /**
     * Razão Social ou Nome
     */
    public function getRazaoSocial($normalize = false)
    {
        if (!$normalize) {
            return $this->razao_social;
        }
        return $this->razao_social;
    }

    public function setRazaoSocial($razao_social)
    {
        $this->razao_social = $razao_social;
        return $this;
    }

    /**
     * Identificador da pessoa na receita
     */
    public function getCNPJ($normalize = false)
    {
        if (!$normalize) {
            return $this->cnpj;
        }
        return $this->cnpj;
    }

    public function setCNPJ($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Inscrição Estadual
     */
    public function getIE($normalize = false)
    {
        if (!$normalize) {
            return $this->ie;
        }
        return $this->ie;
    }

    public function setIE($ie)
    {
        $this->ie = $ie;
        return $this;
    }

    /**
     * Inscrição Municipal
     */
    public function getIM($normalize = false)
    {
        if (!$normalize) {
            return $this->im;
        }
        return $this->im;
    }

    public function setIM($im)
    {
        $this->im = $im;
        return $this;
    }

    /**
     * Dados do endereço
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
        return $this;
    }

    public function getTelefone($normalize = false)
    {
        if (!$normalize) {
            return $this->telefone;
        }
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $pessoa = array();
        $pessoa['razao_social'] = $this->getRazaoSocial();
        $pessoa['cnpj'] = $this->getCNPJ();
        $pessoa['ie'] = $this->getIE();
        $pessoa['im'] = $this->getIM();
        if (!is_null($this->getEndereco()) && $recursive) {
            $pessoa['endereco'] = $this->getEndereco()->toArray($recursive);
        } else {
            $pessoa['endereco'] = $this->getEndereco();
        }
        $pessoa['telefone'] = $this->getTelefone();
        return $pessoa;
    }

    public function fromArray($pessoa = array())
    {
        if ($pessoa instanceof Pessoa) {
            $pessoa = $pessoa->toArray();
        } elseif (!is_array($pessoa)) {
            return $this;
        }
        if (isset($pessoa['razao_social'])) {
            $this->setRazaoSocial($pessoa['razao_social']);
        } else {
            $this->setRazaoSocial(null);
        }
        if (isset($pessoa['cnpj'])) {
            $this->setCNPJ($pessoa['cnpj']);
        } else {
            $this->setCNPJ(null);
        }
        if (isset($pessoa['ie'])) {
            $this->setIE($pessoa['ie']);
        } else {
            $this->setIE(null);
        }
        if (isset($pessoa['im'])) {
            $this->setIM($pessoa['im']);
        } else {
            $this->setIM(null);
        }
        if (!array_key_exists('endereco', $pessoa)) {
            $this->setEndereco(new Endereco());
        } else {
            $this->setEndereco($pessoa['endereco']);
        }
        if (isset($pessoa['telefone'])) {
            $this->setTelefone($pessoa['telefone']);
        } else {
            $this->setTelefone(null);
        }
        return $this;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'emit':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $razao_social = Util::loadNode($element, 'xNome');
        if (is_null($razao_social) && $this instanceof Emitente) {
            throw new \Exception('Tag "xNome" do campo "RazaoSocial" não encontrada', 404);
        }
        $this->setRazaoSocial($razao_social);
        $cnpj = Util::loadNode($element, 'CNPJ');
        if (is_null($cnpj) && $this instanceof Emitente) {
            throw new \Exception('Tag "CNPJ" do campo "CNPJ" não encontrada', 404);
        }
        $this->setCNPJ($cnpj);
        $ie = Util::loadNode($element, 'IE');
        if (is_null($ie) && $this instanceof Emitente) {
            throw new \Exception('Tag "IE" do campo "IE" não encontrada', 404);
        }
        $this->setIE($ie);
        $this->setIM(Util::loadNode($element, 'IM'));
        if ($this instanceof Emitente) {
            $tag_ender = 'enderEmit';
        } else {
            $tag_ender = 'enderDest';
        }
        $endereco = null;
        $_fields = $element->getElementsByTagName($tag_ender);
        if ($_fields->length > 0) {
            $endereco = new Endereco();
            $endereco->loadNode($_fields->item(0), $tag_ender);
        } elseif ($this instanceof Emitente) {
            throw new \Exception('Tag "'.$tag_ender.'" do objeto "Endereco" não encontrada', 404);
        }
        $this->setEndereco($endereco);
        $telefone = null;
        if ($_fields->length > 0) {
            $telefone = Util::loadNode($_fields->item(0), 'fone');
        }
        $this->setTelefone($telefone);
        return $element;
    }
}
