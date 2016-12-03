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
class Endereco implements NodeInterface {

	private $pais;
	private $cep;
	private $municipio;
	private $bairro;
	private $logradouro;
	private $numero;
	private $complemento;

	public function __construct($endereco = array()) {
		$this->fromArray($endereco);
	}

	public function getPais() {
		return $this->pais;
	}

	public function setPais($pais) {
		$this->pais = $pais;
		return $this;
	}

	public function getCEP($normalize = false) {
		if(!$normalize)
			return $this->cep;
		return $this->cep;
	}

	public function setCEP($cep) {
		$this->cep = $cep;
		return $this;
	}

	public function getMunicipio() {
		return $this->municipio;
	}

	public function setMunicipio($municipio) {
		$this->municipio = $municipio;
		return $this;
	}

	public function getBairro($normalize = false) {
		if(!$normalize)
			return $this->bairro;
		return $this->bairro;
	}

	public function setBairro($bairro) {
		$this->bairro = $bairro;
		return $this;
	}

	public function getLogradouro($normalize = false) {
		if(!$normalize)
			return $this->logradouro;
		return $this->logradouro;
	}

	public function setLogradouro($logradouro) {
		$this->logradouro = $logradouro;
		return $this;
	}

	public function getNumero($normalize = false) {
		if(!$normalize)
			return $this->numero;
		return $this->numero;
	}

	public function setNumero($numero) {
		$this->numero = $numero;
		return $this;
	}

	public function getComplemento($normalize = false) {
		if(!$normalize)
			return $this->complemento;
		return $this->complemento;
	}

	public function setComplemento($complemento) {
		$this->complemento = $complemento;
		return $this;
	}

	public function getEndereco($normalize = false) {
		return $this->getLogradouro().', '.$this->getNumero().' - '.$this->getBairro();
	}

	public function toArray() {
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

	public function fromArray($endereco = array()) {
		if($endereco instanceof Endereco)
			$endereco = $endereco->toArray();
		else if(!is_array($endereco))
			return $this;
		$this->setPais($endereco['pais']);
		if(is_null($this->getPais()))
			$this->setPais(new Pais(array('codigo' => 1058, 'nome' => 'Brasil')));
		$this->setCEP($endereco['cep']);
		$this->setMunicipio($endereco['municipio']);
		if(is_null($this->getMunicipio()))
			$this->setMunicipio(new Municipio());
		$this->setBairro($endereco['bairro']);
		$this->setLogradouro($endereco['logradouro']);
		$this->setNumero($endereco['numero']);
		$this->setComplemento($endereco['complemento']);
		return $this;
	}

	public function checkCodigos() {
		$this->getMunicipio()->checkCodigos();
		$this->getMunicipio()->getEstado()->checkCodigos();
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$this->checkCodigos();
		$element = $dom->createElement(is_null($name)?'enderEmit':$name);
		$element->appendChild($dom->createElement('xLgr', $this->getLogradouro(true)));
		$element->appendChild($dom->createElement('nro', $this->getNumero(true)));
		if(!is_null($this->getComplemento()))
			$element->appendChild($dom->createElement('xCpl', $this->getComplemento(true)));
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

}
