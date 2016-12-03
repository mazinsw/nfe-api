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
 * Dados dos transportes da NF-e
 */
class Transporte implements NodeInterface {

	/**
	 * Modalidade do frete
	 * 0- Por conta do emitente;
	 * 1- Por conta do
	 * destinatário/remetente;
	 * 2- Por conta de terceiros;
	 * 9- Sem frete (v2.0)
	 */
	const FRETE_EMITENTE = 'emitente';
	const FRETE_DESTINATARIO = 'destinatario';
	const FRETE_TERCEIROS = 'terceiros';
	const FRETE_NENHUM = 'nenhum';

	private $frete;
	private $transportador;
	private $veiculo;
	private $reboque;
	private $volume;

	public function __construct($transporte = array()) {
		$this->fromArray($transporte);
	}

	/**
	 * Modalidade do frete
	 * 0- Por conta do emitente;
	 * 1- Por conta do
	 * destinatário/remetente;
	 * 2- Por conta de terceiros;
	 * 9- Sem frete (v2.0)
	 */
	public function getFrete($normalize = false) {
		if(!$normalize)
			return $this->frete;
		switch ($this->frete) {
			case self::FRETE_EMITENTE:
				return '0';
			case self::FRETE_DESTINATARIO:
				return '1';
			case self::FRETE_TERCEIROS:
				return '2';
			case self::FRETE_NENHUM:
				return '9';
		}
		return $this->frete;
	}

	public function setFrete($frete) {
		$this->frete = $frete;
		return $this;
	}

	/**
	 * Dados da transportadora
	 */
	public function getTransportador() {
		return $this->transportador;
	}

	public function setTransportador($transportador) {
		$this->transportador = $transportador;
		return $this;
	}

	public function getVeiculo() {
		return $this->veiculo;
	}

	public function setVeiculo($veiculo) {
		$this->veiculo = $veiculo;
		return $this;
	}

	public function getReboque() {
		return $this->reboque;
	}

	public function setReboque($reboque) {
		$this->reboque = $reboque;
		return $this;
	}

	public function getVolume() {
		return $this->volume;
	}

	public function setVolume($volume) {
		$this->volume = $volume;
		return $this;
	}

	public function toArray() {
		$transporte = array();
		$transporte['frete'] = $this->getFrete();
		$transporte['transportador'] = $this->getTransportador();
		$transporte['veiculo'] = $this->getVeiculo();
		$transporte['reboque'] = $this->getReboque();
		$transporte['volume'] = $this->getVolume();
		return $transporte;
	}

	public function fromArray($transporte = array()) {
		if($transporte instanceof Transporte)
			$transporte = $transporte->toArray();
		else if(!is_array($transporte))
			return $this;
		$this->setFrete($transporte['frete']);
		if(is_null($this->getFrete()))
			$this->setFrete(self::FRETE_NENHUM);
		$this->setTransportador($transporte['transportador']);
		if(is_null($this->getTransportador()))
			$this->setTransportador(new \Transporte\Transportador());
		$this->setVeiculo($transporte['veiculo']);
		if(is_null($this->getVeiculo()))
			$this->setVeiculo(new \Transporte\Veiculo());
		$this->setReboque($transporte['reboque']);
		if(is_null($this->getReboque()))
			$this->setReboque(new \Transporte\Veiculo());
		$this->setVolume($transporte['volume']);
		if(is_null($this->getVolume()))
			$this->setVolume(new Volume());
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'transp':$name);
		$element->appendChild($dom->createElement('modFrete', $this->getFrete(true)));
		if($this->getFrete() == self::FRETE_NENHUM)
			return $element;
		if(!is_null($this->getTransportador())) {
			$transportador = $this->getTransportador()->getNode();
			$transportador = $dom->importNode($transportador, true);
			$element->appendChild($transportador);
		}
		if(!is_null($this->getVeiculo())) {
			$veiculo = $this->getVeiculo()->getNode('veicTransp');
			$veiculo = $dom->importNode($veiculo, true);
			$element->appendChild($veiculo);
		}
		if(!is_null($this->getReboque())) {
			$reboque = $this->getReboque()->getNode('reboque');
			$reboque = $dom->importNode($reboque, true);
			$element->appendChild($reboque);
		}
		if(!is_null($this->getVolume())) {
			$volume = $this->getVolume()->getNode();
			$volume = $dom->importNode($volume, true);
			$element->appendChild($volume);
		}
		return $element;
	}

}