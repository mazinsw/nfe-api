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

    public function toArray($recursive = false)
    {
        $transportador = parent::toArray($recursive);
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
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $cnpj = Util::loadNode($element, 'CNPJ');
        $cpf = Util::loadNode($element, 'CPF');
        if (is_null($cnpj) && is_null($cpf)) {
            throw new \Exception('Tag "CNPJ" ou "CPF" não encontrada no Transportador', 404);
        }
        $this->setCNPJ($cnpj);
        $this->setCPF($cpf);
        if (!is_null($this->getCNPJ())) {
            $this->setRazaoSocial(
                Util::loadNode(
                    $element,
                    'xNome',
                    'Tag "xNome" do campo "RazaoSocial" não encontrada'
                )
            );
        } else {
            $this->setNome(
                Util::loadNode(
                    $element,
                    'xNome',
                    'Tag "xNome" do campo "Nome" não encontrada'
                )
            );
        }
        $this->setIE(
            Util::loadNode(
                $element,
                'IE',
                'Tag "IE" do campo "IE" não encontrada'
            )
        );
        $this->setIM(null);
        $descricao = Util::loadNode($element, 'xEnder');
        if (is_null($descricao)) {
            $this->setEndereco(null);
            return $element;
        }
        $endereco = new \NFe\Entity\Endereco();
        $endereco->parseDescricao($descricao);
        $endereco->getMunicipio()->setNome(
            Util::loadNode(
                $element,
                'xMun',
                'Tag "xMun" do nome do município não encontrada'
            )
        );
        $endereco->getMunicipio()->getEstado()->setUF(
            Util::loadNode(
                $element,
                'UF',
                'Tag "UF" da UF do estado não encontrada'
            )
        );
        $this->setEndereco($endereco);
        return $element;
    }
}
