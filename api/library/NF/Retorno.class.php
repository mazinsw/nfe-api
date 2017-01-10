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
namespace NF;
use Util;

class Retorno extends Status {

	private $data_recebimento;
	private $numero;

	public function __construct($retorno = array()) {
		parent::__construct($retorno);
	}

	public function getDataRecebimento($normalize = false) {
		if(!$normalize)
			return $this->data_recebimento;
		return Util::toDateTime($this->data_recebimento);
	}

	public function setDataRecebimento($data_recebimento) {
		$this->data_recebimento = $data_recebimento;
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

	public function toArray() {
		$retorno = parent::toArray();
		$retorno['data_recebimento'] = $this->getDataRecebimento();
		$retorno['numero'] = $this->getNumero();
		return $retorno;
	}

	public function fromArray($retorno = array()) {
		if($retorno instanceof Retorno)
			$retorno = $retorno->toArray();
		else if(!is_array($retorno))
			return $this;
		parent::fromArray($retorno);
		$this->setDataRecebimento($retorno['data_recebimento']);
		$this->setNumero($retorno['numero']);
		return $this;
	}

	public function getNode($name = null) {
		$element = parent::getNode(is_null($name)?'':$name);
		$dom = $element->ownerDocument;
		$status = $element->getElementsByTagName('cStat')->item(0);
		if(!is_null($this->getDataRecebimento()))
			$element->insertBefore($dom->createElement('dhRecbto', $this->getDataRecebimento(true)), $status);
		if(!is_null($this->getNumero()))
			$element->insertBefore($dom->createElement('nProt', $this->getNumero(true)), $status);
		return $element;
	}

	public function loadNode($dom, $name = null) {
		$tag = is_null($name)?'Retorno':$name;
		parent::loadNode($dom, $tag);
		$retorno = $dom->getElementsByTagName($tag)->item(0);
		$nodes = $retorno->getElementsByTagName('dhRecbto');
		$data_recebimento = null;
		if($nodes->length > 0)
			$data_recebimento = strtotime($nodes->item(0)->nodeValue);
		$this->setDataRecebimento($data_recebimento);
		$nodes = $retorno->getElementsByTagName('nProt');
		$numero = null;
		if($nodes->length > 0)
			$numero = $nodes->item(0)->nodeValue;
		$this->setNumero($numero);
		return $this;
	}

}