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
namespace Imposto\COFINS;
use Util;
use Imposto;
use Exception;
use DOMDocument;

class Quantidade extends Imposto {

	public function __construct($cofins = array()) {
		parent::__construct($cofins);
		$this->setGrupo(self::GRUPO_COFINS);
		$this->setTributacao('03');
	}

	public function getQuantidade($normalize = false) {
		if(!$normalize)
			return $this->getBase();
		return Util::toFloat($this->getBase());
	}

	public function setQuantidade($quantidade) {
		return $this->setBase($quantidade);
	}

	/**
	 * Calcula o valor do imposto com base na quantidade e no valor da aliquota
	 */
	public function getValor($normalize = false) {
		if(!$normalize)
			return $this->getQuantidade() * $this->getAliquota();
		$valor = $this->getValor();
		return Util::toCurrency($valor);
	}

	public function toArray() {
		$cofins = parent::toArray();
		return $cofins;
	}

	public function fromArray($cofins = array()) {
		if($cofins instanceof Quantidade)
			$cofins = $cofins->toArray();
		else if(!is_array($cofins))
			return $this;
		parent::fromArray($cofins);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'COFINSQtde':$name);
		$element->appendChild($dom->createElement('CST', $this->getTributacao(true)));
		$element->appendChild($dom->createElement('qBCProd', $this->getQuantidade(true)));
		$element->appendChild($dom->createElement('vAliqProd', $this->getAliquota(true)));
		$element->appendChild($dom->createElement('vCOFINS', $this->getValor(true)));
		return $element;
	}

	public function loadNode($element, $name = null) {
		$name = is_null($name)?'COFINSQtde':$name;
		if($element->tagName != $name) {
			$_fields = $element->getElementsByTagName($name);
			if($_fields->length == 0)
				throw new Exception('Tag "'.$name.'" não encontrada', 404);
			$element = $_fields->item(0);
		}
		$_fields = $element->getElementsByTagName('CST');
		if($_fields->length > 0)
			$tributacao = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "CST" do campo "Tributacao" não encontrada', 404);
		$this->setTributacao($tributacao);
		$_fields = $element->getElementsByTagName('qBCProd');
		if($_fields->length > 0)
			$quantidade = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "qBCProd" do campo "Quantidade" não encontrada', 404);
		$this->setQuantidade($quantidade);
		$_fields = $element->getElementsByTagName('vAliqProd');
		if($_fields->length > 0)
			$aliquota = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "vAliqProd" do campo "Aliquota" não encontrada', 404);
		$this->setAliquota($aliquota);
		return $element;
	}

}