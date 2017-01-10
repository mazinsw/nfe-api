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
use NF;
use DOMDocument;

class Protocolo extends Retorno {

	private $chave;
	private $validacao;

	public function __construct($protocolo = array()) {
		parent::__construct($protocolo);
	}

	public function getChave($normalize = false) {
		if(!$normalize)
			return $this->chave;
		return $this->chave;
	}

	public function setChave($chave) {
		$this->chave = $chave;
		return $this;
	}

	public function getValidacao($normalize = false) {
		if(!$normalize)
			return $this->validacao;
		return $this->validacao;
	}

	public function setValidacao($validacao) {
		$this->validacao = $validacao;
		return $this;
	}

	public function toArray() {
		$protocolo = parent::toArray();
		$protocolo['chave'] = $this->getChave();
		$protocolo['validacao'] = $this->getValidacao();
		return $protocolo;
	}

	public function fromArray($protocolo = array()) {
		if($protocolo instanceof Protocolo)
			$protocolo = $protocolo->toArray();
		else if(!is_array($protocolo))
			return $this;
		parent::fromArray($protocolo);
		$this->setChave($protocolo['chave']);
		$this->setValidacao($protocolo['validacao']);
		return $this;
	}

	public function loadNode($dom, $name = null) {
		$tag = is_null($name)?'infProt':$name;
		parent::loadNode($dom, $tag);

		$info = $dom->getElementsByTagName($tag)->item(0);
		$this->setChave($info->getElementsByTagName('chNFe')->item(0)->nodeValue);
		$this->setValidacao($info->getElementsByTagName('digVal')->item(0)->nodeValue);
		return $this;
	}

	public function getNode($name = null) {
		$old_uf = $this->getUF();
		$this->setUF(null);
		$info = parent::getNode('infProt');
		$this->setUF($old_uf);
		$dom = $info->ownerDocument;
		$element = $dom->createElement(is_null($name)?'protNFe':$name);
		$versao = $dom->createAttribute('versao');
		$versao->value = NF::VERSAO;
		$element->appendChild($versao);

		$id = $dom->createAttribute('Id');
		$id->value = 'ID'.$this->getNumero(true);
		$info->appendChild($id);

		$status = $info->getElementsByTagName('cStat')->item(0);
		$info->insertBefore($dom->createElement('digVal', $this->getValidacao(true)), $status);
		$nodes = $info->getElementsByTagName('dhRecbto');
		if($nodes->length > 0)
			$recebimento = $nodes->item(0);
		else
			$recebimento = $status;
		$info->insertBefore($dom->createElement('chNFe', $this->getChave(true)), $recebimento);
		$element->appendChild($info);
		return $element;
	}

}