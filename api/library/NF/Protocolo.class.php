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

class Protocolo extends Autorizacao {

	private $chave;
	private $validacao;
	private $numero;

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

	public function getNumero($normalize = false) {
		if(!$normalize)
			return $this->numero;
		return 'ID'.$this->numero;
	}

	public function setNumero($numero) {
		$this->numero = $numero;
		return $this;
	}

	public function toArray() {
		$protocolo = parent::toArray();
		$protocolo['chave'] = $this->getChave();
		$protocolo['validacao'] = $this->getValidacao();
		$protocolo['numero'] = $this->getNumero();
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
		$this->setNumero($protocolo['numero']);
		return $this;
	}

	public function carrega(&$dom) {
		$info = $dom->getElementsByTagName('infProt')->item(0);
		$this->setAmbiente($info->getElementsByTagName('tpAmb')->item(0)->nodeValue);
		$this->setVersao($info->getElementsByTagName('verAplic')->item(0)->nodeValue);
		$this->setChave($info->getElementsByTagName('chNFe')->item(0)->nodeValue);
		$this->setNumero($info->getElementsByTagName('nProt')->item(0)->nodeValue);
		$this->setValidacao($info->getElementsByTagName('digVal')->item(0)->nodeValue);
		$this->setStatus($info->getElementsByTagName('cStat')->item(0)->nodeValue);
		$this->setMotivo($info->getElementsByTagName('xMotivo')->item(0)->nodeValue);
		$data_recebimento = $info->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
		$data_recebimento = strtotime($data_recebimento);
		$this->setDataRecebimento($data_recebimento);
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'protNFe':$name);
		$versao = $dom->createAttribute('versao');
		$versao->value = NF::VERSAO;
		$element->appendChild($versao);

		$info = $dom->createElement('infProt');
		$id = $dom->createAttribute('Id');
		$id->value = $this->getNumero(true);
		$info->appendChild($id);

		$info->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
		$info->appendChild($dom->createElement('verAplic', $this->getVersao(true)));
		$info->appendChild($dom->createElement('chNFe', $this->getChave(true)));
		$info->appendChild($dom->createElement('dhRecbto', $this->getDataRecebimento(true)));
		$info->appendChild($dom->createElement('nProt', $this->getNumero(false)));
		$info->appendChild($dom->createElement('digVal', $this->getValidacao(true)));
		$info->appendChild($dom->createElement('cStat', $this->getStatus(true)));
		$info->appendChild($dom->createElement('xMotivo', $this->getMotivo(true)));
		$element->appendChild($info);

		return $element;
	}

}