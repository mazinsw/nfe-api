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
 * Imposto sobre compra de mercadoria e prestação de serviços
 */
class ICMS implements NodeInterface {

	private $id;

	public function __construct($icms = array()) {
		$this->fromArray($icms);
	}

	/**
	 * Identificador do ICMS, Ex.: ICMS00
	 */
	public function getID($normalize = false) {
		if(!$normalize)
			return $this->id;
		return $this->id;
	}

	public function setID($id) {
		$this->id = $id;
		return $this;
	}

	public function toArray() {
		$icms = array();
		$icms['id'] = $this->getID();
		return $icms;
	}

	public function fromArray($icms = array()) {
		if($icms instanceof ICMS)
			$icms = $icms->toArray();
		else if(!is_array($icms))
			return $this;
		$this->setID($icms['id']);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'ICMS':$name);
		$element->appendChild($dom->createElement('ICMS', $this->getID(true)));
		return $element;
	}

}