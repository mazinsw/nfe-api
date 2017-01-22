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
use Util;
use Estado;
use Exception;
use DOMDocument;
use NodeInterface;

class Status implements NodeInterface {

	private $ambiente;
	private $versao;
	private $status;
	private $motivo;
	private $uf;

	public function __construct($status = array()) {
		$this->fromArray($status);
	}

	/**
	 * Identificação do Ambiente: 1 - Produção, 2 - Homologação
	 */
	public function getAmbiente($normalize = false) {
		if(!$normalize)
			return $this->ambiente;
		switch ($this->ambiente) {
			case NF::AMBIENTE_PRODUCAO:
				return '1';
			case NF::AMBIENTE_HOMOLOGACAO:
				return '2';
		}
		return $this->ambiente;
	}

	public function setAmbiente($ambiente) {
		switch ($ambiente) {
			case '1':
				$ambiente = NF::AMBIENTE_PRODUCAO;
				break;
			case '2':
				$ambiente = NF::AMBIENTE_HOMOLOGACAO;
				break;
		}
		$this->ambiente = $ambiente;
		return $this;
	}

	public function getVersao($normalize = false) {
		if(!$normalize)
			return $this->versao;
		return $this->versao;
	}

	public function setVersao($versao) {
		$this->versao = $versao;
		return $this;
	}

	public function getStatus($normalize = false) {
		if(!$normalize)
			return $this->status;
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	public function getMotivo($normalize = false) {
		if(!$normalize)
			return $this->motivo;
		return $this->motivo;
	}

	public function setMotivo($motivo) {
		$this->motivo = $motivo;
		return $this;
	}

	public function getUF($normalize = false) {
		if(!$normalize || is_numeric($this->uf))
			return $this->uf;

		$estado = new Estado();
		$estado->setUF($this->uf);
		$estado->checkCodigos();
		return $estado->getCodigo();
	}

	public function setUF($uf) {
		$this->uf = $uf;
		return $this;
	}

	public static function genLote() {
		return substr(Util::padText(number_format(microtime(true)*1000000, 0, '', ''), 15), 0, 15);
	}

	public function toArray() {
		$status = array();
		$status['ambiente'] = $this->getAmbiente();
		$status['versao'] = $this->getVersao();
		$status['status'] = $this->getStatus();
		$status['motivo'] = $this->getMotivo();
		$status['uf'] = $this->getUF();
		return $status;
	}

	public function fromArray($status = array()) {
		if($status instanceof Status)
			$status = $status->toArray();
		else if(!is_array($status))
			return $this;
		$this->setAmbiente($status['ambiente']);
		$this->setVersao($status['versao']);
		$this->setStatus($status['status']);
		$this->setMotivo($status['motivo']);
		$this->setUF($status['uf']);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'Status':$name);
		$element->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
		$element->appendChild($dom->createElement('verAplic', $this->getVersao(true)));
		$element->appendChild($dom->createElement('cStat', $this->getStatus(true)));
		$element->appendChild($dom->createElement('xMotivo', $this->getMotivo(true)));
		if(!is_null($this->getUF()))
			$element->appendChild($dom->createElement('cUF', $this->getUF(true)));
		return $element;
	}

	public function loadNode($element, $name = null) {
		$name = is_null($name)?'Status':$name;
		$nodes = $element->getElementsByTagName($name);
		if($nodes->length == 0)
			throw new Exception('Tag "'.$name.'" não encontrada', 404);
		$status = $nodes->item(0);
		$this->setAmbiente($status->getElementsByTagName('tpAmb')->item(0)->nodeValue);
		$this->setVersao($status->getElementsByTagName('verAplic')->item(0)->nodeValue);
		$this->setStatus($status->getElementsByTagName('cStat')->item(0)->nodeValue);
		$this->setMotivo($status->getElementsByTagName('xMotivo')->item(0)->nodeValue);
		$nodes = $status->getElementsByTagName('cUF');
		$uf = null;
		if($nodes->length > 0)
			$uf = $nodes->item(0)->nodeValue;
		$this->setUF($uf);
		return $status;
	}

}