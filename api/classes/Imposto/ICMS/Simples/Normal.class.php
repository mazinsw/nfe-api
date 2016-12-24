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
use DOMDocument;
use Imposto\ICMS\Normal as ICMSNormal;

/**
 * Tributada pelo Simples Nacional com permissão de crédito
 */
class Normal extends ICMSNormal {

	public function __construct($normal = array()) {
		parent::__construct($normal);
		$this->setTributacao('101');
	}

	public function toArray() {
		$normal = parent::toArray();
		return $normal;
	}

	public function fromArray($normal = array()) {
		if($normal instanceof Normal)
			$normal = $normal->toArray();
		else if(!is_array($normal))
			return $this;
		parent::fromArray($normal);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'ICMSSN101':$name);
		$element->appendChild($dom->createElement('orig', $this->getOrigem(true)));
		$element->appendChild($dom->createElement('CSOSN', $this->getTributacao(true)));
		$element->appendChild($dom->createElement('pCredSN', $this->getAliquota(true)));
		$element->appendChild($dom->createElement('vCredICMSSN', $this->getValor(true)));
		return $element;
	}

}