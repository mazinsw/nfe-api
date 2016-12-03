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
namespace Imposto\ICMS;
use Util;

/**
 * Tributação pelo ICMS
 * 60 - ICMS cobrado anteriormente por substituição
 * tributária
 */
class Cobrado extends Generico {

	private $valor;

	public function __construct($cobrado = array()) {
		parent::__construct($cobrado);
		$this->setTributacao('60');
	}

	public function getValor($normalize = false) {
		if(!$normalize)
			return $this->valor;
		return Util::toCurrency($this->valor);
	}

	public function setValor($valor) {
		$this->valor = $valor;
		return $this;
	}

	public function toArray() {
		$cobrado = parent::toArray();
		$cobrado['valor'] = $this->getValor();
		return $cobrado;
	}

	public function fromArray($cobrado = array()) {
		if($cobrado instanceof Cobrado)
			$cobrado = $cobrado->toArray();
		else if(!is_array($cobrado))
			return $this;
		parent::fromArray($cobrado);
		$this->setValor($cobrado['valor']);
		return $this;
	}

	public function getNode($name = null) {
		$element = parent::getNode(is_null($name)?'ICMS60':$name);
		$dom = $element->ownerDocument;
		$element->appendChild($dom->createElement('vBCSTRet', $this->getBase(true)));
		$element->appendChild($dom->createElement('vICMSSTRet', $this->getValor(true)));
		return $element;
	}

}