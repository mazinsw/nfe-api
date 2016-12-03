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
namespace Transporte;
use Cliente;
use DOMDocument;

/**
 * Dados da transportadora
 */
class Transportador extends Cliente {

	public function __construct($transportador = array()) {
		parent::__construct($transportador);
	}

	public function toArray() {
		$transportador = parent::toArray();
		return $transportador;
	}

	public function fromArray($transportador = array()) {
		if($transportador instanceof Transportador)
			$transportador = $transportador->toArray();
		else if(!is_array($transportador))
			return $this;
		parent::fromArray($transportador);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'transporta':$name);
		if(!is_null($this->getCNPJ()))
			$element->appendChild($dom->createElement('CNPJ', $this->getCNPJ(true)));
		else
			$element->appendChild($dom->createElement('CPF', $this->getCPF(true)));
		if(!is_null($this->getCNPJ()))
			$element->appendChild($dom->createElement('xNome', $this->getRazaoSocial(true)));
		else
			$element->appendChild($dom->createElement('xNome', $this->getNome(true)));
		if(!is_null($this->getCNPJ())) {
			$element->appendChild($dom->createElement('IE', $this->getIE(true)));
		}
		if(!is_null($this->getEndereco())) {
			$endereco = $this->getEndereco();
			$element->appendChild($dom->createElement('xEnder', $endereco->getEndereco(true)));
			$element->appendChild($dom->createElement('xMun', $endereco->getMunicipio()->getNome(true)));
			$element->appendChild($dom->createElement('UF', $endereco->getMunicipio()->getEstado()->getUF(true)));
		}
		return $element;
	}

}