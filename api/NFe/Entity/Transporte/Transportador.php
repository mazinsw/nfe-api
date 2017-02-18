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
namespace NFe\Entity\Transporte;

use NFe\Common\Util;
use NFe\Entity\Destinatario;

/**
 * Dados da transportadora
 */
class Transportador extends Destinatario
{

    public function __construct($transportador = array())
    {
        parent::__construct($transportador);
    }

    public function toArray()
    {
        $transportador = parent::toArray();
        return $transportador;
    }

    public function fromArray($transportador = array())
    {
        if ($transportador instanceof Transportador) {
            $transportador = $transportador->toArray();
        } elseif (!is_array($transportador)) {
            return $this;
        }
        parent::fromArray($transportador);
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'transporta':$name);
        if (!is_null($this->getCNPJ())) {
            Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        } else {
            Util::appendNode($element, 'CPF', $this->getCPF(true));
        }
        if (!is_null($this->getCNPJ())) {
            Util::appendNode($element, 'xNome', $this->getRazaoSocial(true));
        } else {
            Util::appendNode($element, 'xNome', $this->getNome(true));
        }
        if (!is_null($this->getCNPJ())) {
            Util::appendNode($element, 'IE', $this->getIE(true));
        }
        if (!is_null($this->getEndereco())) {
            $endereco = $this->getEndereco();
            Util::appendNode($element, 'xEnder', $endereco->getDescricao(true));
            Util::appendNode($element, 'xMun', $endereco->getMunicipio()->getNome(true));
            Util::appendNode($element, 'UF', $endereco->getMunicipio()->getEstado()->getUF(true));
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'transporta':$name;
        if ($element->tagName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $cnpj = null;
        $cpf = null;
        $_fields = $element->getElementsByTagName('CNPJ');
        if ($_fields->length == 0) {
            $_fields = $element->getElementsByTagName('CPF');
        }
        if ($_fields->length > 0) {
            if ($_fields->item(0)->tagName == 'CNPJ') {
                $cnpj = $_fields->item(0)->nodeValue;
            } else {
                $cpf = $_fields->item(0)->nodeValue;
            }
        } else {
            throw new \Exception('Tag "CNPJ ou CPF" do campo "CNPJ ou CPF" não encontrada', 404);
        }
        $this->setCNPJ($cnpj);
        $this->setCPF($cpf);
        $_fields = $element->getElementsByTagName('xNome');
        if ($_fields->length > 0) {
            $nome = $_fields->item(0)->nodeValue;
        } elseif (!is_null($this->getCNPJ())) {
            throw new \Exception('Tag "xNome" do campo "RazaoSocial" não encontrada', 404);
        } else {
            throw new \Exception('Tag "xNome" do campo "Nome" não encontrada', 404);
        }
        if (!is_null($this->getCNPJ())) {
            $this->setRazaoSocial($nome);
        } else {
            $this->setNome($nome);
        }
        $ie = null;
        $this->setIE(
            Util::loadNode(
                $element,
                'IE',
                'Tag "IE" do campo "IE" não encontrada'
            )
        );
        $this->setIM(null);
        $_fields = $element->getElementsByTagName('xEnder');
        if ($_fields->length == 0) {
            $this->setEndereco(null);
            return $element;
        }
        $endereco = new \NFe\Entity\Endereco();
        $endereco->parseDescricao($_fields->item(0)->nodeValue);
        $_fields = $element->getElementsByTagName('xMun');
        if ($_fields->length > 0) {
            $nome_municipio = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "xMun" do nome do município não encontrada', 404);
        }
        $endereco->getMunicipio()->setNome($nome_municipio);
        $_fields = $element->getElementsByTagName('UF');
        if ($_fields->length > 0) {
            $uf_estado = $_fields->item(0)->nodeValue;
        } else {
            throw new \Exception('Tag "UF" da UF do estado não encontrada', 404);
        }
        $endereco->getMunicipio()->getEstado()->setUF($uf_estado);
        $this->setEndereco($endereco);
        return $element;
    }
}
