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
namespace Imposto\ICMS\Simples;
use Util;

/**
 * ICMS cobrado anteriormente por substituição tributária (substituído) ou
 * por antecipação
 */
class Cobrado extends Generico {

	private $valor;

	public function __construct($cobrado = array()) {
		parent::__construct($cobrado);
		$this->setTributacao('500');
	}

	/**
	 * Valor base para cálculo do imposto
	 */
	public function getBase($normalize = false) {
		if(!$normalize)
			return is_null($this->getValor())?0.00:parent::getBase($normalize);
		return Util::toCurrency($this->getBase());
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
		$element = parent::getNode(is_null($name)?'ICMSSN500':$name);
		$dom = $element->ownerDocument;
		if(is_null($this->getValor()))
			return $element;
		$element->appendChild($dom->createElement('vBCSTRet', $this->getBase(true)));
		$element->appendChild($dom->createElement('vICMSSTRet', $this->getValor(true)));
		return $element;
	}

	public function loadNode($element, $name = null) {
		$name = is_null($name)?'ICMSSN500':$name;
		$element = parent::loadNode($element, $name);
		$base = null;
		$_fields = $element->getElementsByTagName('vBCSTRet');
		if($_fields->length > 0)
			$base = $_fields->item(0)->nodeValue;
		$this->setBase($base);
		$valor = null;
		$_fields = $element->getElementsByTagName('vICMSSTRet');
		if($_fields->length > 0)
			$valor = $_fields->item(0)->nodeValue;
		$this->setValor($valor);
		return $element;
	}

}