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

/**
 * Informação de endereço que será informado nos clientes e no emitente
 */
class Endereco implements NodeInterface
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

    public function getEndereco($normalize = false)
    {
        return $this->getLogradouro().', '.$this->getNumero().' - '.$this->getBairro();
    }

    public function toArray()
    {
        $endereco = array();
        $endereco['pais'] = $this->getPais();
        $endereco['cep'] = $this->getCEP();
        $endereco['municipio'] = $this->getMunicipio();
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
        $dom = new DOMDocument('1.0', 'UTF-8');
        $this->checkCodigos();
        $element = $dom->createElement(is_null($name)?'enderEmit':$name);
        $element->appendChild($dom->createElement('xLgr', $this->getLogradouro(true)));
        $element->appendChild($dom->createElement('nro', $this->getNumero(true)));
        if (!is_null($this->getComplemento())) {
            $element->appendChild($dom->createElement('xCpl', $this->getComplemento(true)));
        }
        $element->appendChild($dom->createElement('xBairro', $this->getBairro(true)));
        $element->appendChild($dom->createElement('cMun', $this->getMunicipio()->getCodigo(true)));
        $element->appendChild($dom->createElement('xMun', $this->getMunicipio()->getNome(true)));
        $element->appendChild($dom->createElement('UF', $this->getMunicipio()->getEstado()->getUF(true)));
        $element->appendChild($dom->createElement('CEP', $this->getCEP(true)));
        $element->appendChild($dom->createElement('cPais', $this->getPais()->getCodigo(true)));
        $element->appendChild($dom->createElement('xPais', $this->getPais()->getNome(true)));
        // $element->appendChild($dom->createElement('fone', $this->getTelefone(true)));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'enderEmit':$name;
        if ($element->tagName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $_fields = $element->getElementsByTagName('xLgr');
        if ($_fields->length > 0) {
            $logradouro = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "xLgr" do campo "Logradouro" não encontrada', 404);
        }
        $this->setLogradouro($logradouro);
        $_fields = $element->getElementsByTagName('nro');
        if ($_fields->length > 0) {
            $numero = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "nro" do campo "Numero" não encontrada', 404);
        }
        $this->setNumero($numero);
        $_fields = $element->getElementsByTagName('xCpl');
        $complemento = null;
        if ($_fields->length > 0) {
            $complemento = $_fields->item(0)->nodeValue;
        }
        $this->setComplemento($complemento);
        $_fields = $element->getElementsByTagName('xBairro');
        if ($_fields->length > 0) {
            $bairro = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "xBairro" do campo "Bairro" não encontrada', 404);
        }
        $this->setBairro($bairro);
        $_fields = $element->getElementsByTagName('cMun');
        if ($_fields->length > 0) {
            $codigo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "cMun" do objeto "Municipio" não encontrada', 404);
        }
        $this->getMunicipio()->setCodigo($codigo);
        $_fields = $element->getElementsByTagName('xMun');
        if ($_fields->length > 0) {
            $nome = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "xMun" do objeto "Municipio" não encontrada', 404);
        }
        $this->getMunicipio()->setNome($nome);
        $_fields = $element->getElementsByTagName('UF');
        if ($_fields->length > 0) {
            $uf = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "UF" do campo "UF" não encontrada', 404);
        }
        $this->getMunicipio()->getEstado()->setUF($uf);
        $_fields = $element->getElementsByTagName('CEP');
        if ($_fields->length > 0) {
            $cep = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "CEP" do campo "CEP" não encontrada', 404);
        }
        $this->setCEP($cep);
        $_fields = $element->getElementsByTagName('cPais');
        if ($_fields->length > 0) {
            $codigo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "cPais" do objeto "Pais" não encontrada', 404);
        }
        $this->getPais()->setCodigo($codigo);
        $_fields = $element->getElementsByTagName('xPais');
        if ($_fields->length > 0) {
            $nome = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "xPais" do objeto "Pais" não encontrada', 404);
        }
        $this->getPais()->setNome($nome);
        return $element;
    }
}
