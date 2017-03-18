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
 * Informação de endereço que será informado nos clientes e no emitente
 */
class Endereco implements Node
{

    private $pais;
    private $cep;
    private $municipio;
    private $bairro;
    private $logradouro;
    private $numero;
    private $complemento;

    public function __construct($endereco = array())
    {
        $this->fromArray($endereco);
    }

    public function getPais()
    {
        return $this->pais;
    }

    public function setPais($pais)
    {
        $this->pais = $pais;
        return $this;
    }

    public function getCEP($normalize = false)
    {
        if (!$normalize) {
            return $this->cep;
        }
        return $this->cep;
    }

    public function setCEP($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

    public function getBairro($normalize = false)
    {
        if (!$normalize) {
            return $this->bairro;
        }
        return $this->bairro;
    }

    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
        return $this;
    }

    public function getLogradouro($normalize = false)
    {
        if (!$normalize) {
            return $this->logradouro;
        }
        return $this->logradouro;
    }

    public function setLogradouro($logradouro)
    {
        $this->logradouro = $logradouro;
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

    public function getComplemento($normalize = false)
    {
        if (!$normalize) {
            return $this->complemento;
        }
        return $this->complemento;
    }

    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * Obtém as informações básicas do endereço em uma linha de texto
     * @param  boolean $normalize informa se o valor deve ser normalizado para um XML
     * @return string             endereço com logradouro, número e bairro
     */
    public function getDescricao($normalize = false)
    {
        return $this->getLogradouro().', '.$this->getNumero().' - '.$this->getBairro();
    }

    /**
     * Desmembra a descrição e salva as informações do endereço em seu respectivo campo
     * @param  string $descricao linha de endereço com diversas informações
     * @return Endereco retorna a própria instância
     */
    public function parseDescricao($descricao)
    {
        $pattern = '/(.*), (.*) - (.*)/';
        if (!preg_match($pattern, $descricao, $matches)) {
            throw new \Exception('Não foi possível desmembrar a linha de endereço', 500);
        }
        $this->setLogradouro($matches[1]);
        $this->setNumero($matches[2]);
        $this->setBairro($matches[3]);
        return $this;
    }

    public function toArray($recursive = false)
    {
        $endereco = array();
        if (!is_null($this->getPais()) && $recursive) {
            $endereco['pais'] = $this->getPais()->toArray($recursive);
        } else {
            $endereco['pais'] = $this->getPais();
        }
        $endereco['cep'] = $this->getCEP();
        if (!is_null($this->getMunicipio()) && $recursive) {
            $endereco['municipio'] = $this->getMunicipio()->toArray($recursive);
        } else {
            $endereco['municipio'] = $this->getMunicipio();
        }
        $endereco['bairro'] = $this->getBairro();
        $endereco['logradouro'] = $this->getLogradouro();
        $endereco['numero'] = $this->getNumero();
        $endereco['complemento'] = $this->getComplemento();
        return $endereco;
    }

    public function fromArray($endereco = array())
    {
        if ($endereco instanceof Endereco) {
            $endereco = $endereco->toArray();
        } elseif (!is_array($endereco)) {
            return $this;
        }
        if (!isset($endereco['pais']) || is_null($endereco['pais'])) {
            $this->setPais(new Pais(array('codigo' => 1058, 'nome' => 'Brasil')));
        } else {
            $this->setPais($endereco['pais']);
        }
        if (isset($endereco['cep'])) {
            $this->setCEP($endereco['cep']);
        } else {
            $this->setCEP(null);
        }
        if (!isset($endereco['municipio']) || is_null($endereco['municipio'])) {
            $this->setMunicipio(new Municipio());
        } else {
            $this->setMunicipio($endereco['municipio']);
        }
        if (isset($endereco['bairro'])) {
            $this->setBairro($endereco['bairro']);
        } else {
            $this->setBairro(null);
        }
        if (isset($endereco['logradouro'])) {
            $this->setLogradouro($endereco['logradouro']);
        } else {
            $this->setLogradouro(null);
        }
        if (isset($endereco['numero'])) {
            $this->setNumero($endereco['numero']);
        } else {
            $this->setNumero(null);
        }
        if (isset($endereco['complemento'])) {
            $this->setComplemento($endereco['complemento']);
        } else {
            $this->setComplemento(null);
        }
        return $this;
    }

    public function checkCodigos()
    {
        $this->getMunicipio()->checkCodigos();
        $this->getMunicipio()->getEstado()->checkCodigos();
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $this->checkCodigos();
        $element = $dom->createElement(is_null($name)?'enderEmit':$name);
        Util::appendNode($element, 'xLgr', $this->getLogradouro(true));
        Util::appendNode($element, 'nro', $this->getNumero(true));
        if (!is_null($this->getComplemento())) {
            Util::appendNode($element, 'xCpl', $this->getComplemento(true));
        }
        Util::appendNode($element, 'xBairro', $this->getBairro(true));
        Util::appendNode($element, 'cMun', $this->getMunicipio()->getCodigo(true));
        Util::appendNode($element, 'xMun', $this->getMunicipio()->getNome(true));
        Util::appendNode($element, 'UF', $this->getMunicipio()->getEstado()->getUF(true));
        Util::appendNode($element, 'CEP', $this->getCEP(true));
        Util::appendNode($element, 'cPais', $this->getPais()->getCodigo(true));
        Util::appendNode($element, 'xPais', $this->getPais()->getNome(true));
        // Util::appendNode($element, 'fone', $this->getTelefone(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'enderEmit':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setLogradouro(
            Util::loadNode(
                $element,
                'xLgr',
                'Tag "xLgr" do campo "Logradouro" não encontrada'
            )
        );
        $this->setNumero(
            Util::loadNode(
                $element,
                'nro',
                'Tag "nro" do campo "Numero" não encontrada'
            )
        );
        $this->setComplemento(Util::loadNode($element, 'xCpl'));
        $this->setBairro(
            Util::loadNode(
                $element,
                'xBairro',
                'Tag "xBairro" do campo "Bairro" não encontrada'
            )
        );
        $this->getMunicipio()->setCodigo(
            Util::loadNode(
                $element,
                'cMun',
                'Tag "cMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->getMunicipio()->setNome(
            Util::loadNode(
                $element,
                'xMun',
                'Tag "xMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->getMunicipio()->getEstado()->setUF(
            Util::loadNode(
                $element,
                'UF',
                'Tag "UF" do objeto "Estado" não encontrada'
            )
        );
        $this->setCEP(
            Util::loadNode(
                $element,
                'CEP',
                'Tag "CEP" do campo "CEP" não encontrada'
            )
        );
        $this->getPais()->setCodigo(
            Util::loadNode(
                $element,
                'cPais',
                'Tag "cPais" do objeto "Pais" não encontrada'
            )
        );
        $this->getPais()->setNome(
            Util::loadNode(
                $element,
                'xPais',
                'Tag "xPais" do objeto "Pais" não encontrada'
            )
        );
        return $element;
    }
}
