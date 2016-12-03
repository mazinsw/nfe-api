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
namespace Imposto\IPI;
use Imposto;
use DOMDocument;

/**
 * Quantidade x valor Unidade de Produto
 */
class Quantidade extends Imposto {

	public function __construct($quantidade = array()) {
		parent::__construct($quantidade);
	}

	public function getQuantidade($normalize = false) {
		if(!$normalize)
			return $this->getBase();
		return Util::toFloat($this->getBase());
	}

	public function setQuantidade($quantidade) {
		return $this->setBase($quantidade);
	}

	public function getPreco($normalize = false) {
		if(!$normalize)
			return $this->getAliquota();
		return Util::toCurrency($this->getPreco());
	}

	public function setPreco($preco) {
		return $this->setAliquota($preco);
	}

	/**
	 * Calcula o valor do imposto com base na quantidade e no preço
	 */
	public function getValor($normalize = false) {
		if(!$normalize)
			return $this->getQuantidade() * $this->getPreco();
		return Util::toCurrency($this->getValor());
	}

	public function toArray() {
		$quantidade = parent::toArray();
		return $quantidade;
	}

	public function fromArray($quantidade = array()) {
		if($quantidade instanceof Quantidade)
			$quantidade = $quantidade->toArray();
		else if(!is_array($quantidade))
			return $this;
		parent::fromArray($quantidade);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'IPITrib':$name);
		$element->appendChild($dom->createElement('CST', $this->getTributacao(true)));
		$element->appendChild($dom->createElement('qUnid', $this->getQuantidade(true)));
		$element->appendChild($dom->createElement('vUnid', $this->getPreco(true)));
		$element->appendChild($dom->createElement('vIPI', $this->getValor(true)));
		return $element;
	}

}