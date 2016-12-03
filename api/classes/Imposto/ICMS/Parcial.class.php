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
namespace Imposto\ICMS;
use Imposto;
use DOMDocument;
use Util;

/**
 * Tributação pelo ICMS
 * 30 - Isenta ou não tributada e com cobrança do ICMS
 * por substituição tributária, estende de Imposto
 */
class Parcial extends Imposto {

	/**
	 * origem da mercadoria: 0 - Nacional
	 * 1 - Estrangeira - Importação direta
	 * 
	 * 2 - Estrangeira - Adquirida no mercado interno
	 */
	const ORIGEM_NACIONAL = 'nacional';
	const ORIGEM_ESTRANGEIRA = 'estrangeira';
	const ORIGEM_INTERNO = 'interno';

	/**
	 * Modalidade de determinação da BC do ICMS ST:
	 * 0 – Preço tabelado ou
	 * máximo  sugerido;
	 * 1 - Lista Negativa (valor);
	 * 2 - Lista Positiva
	 * (valor);
	 * 3 - Lista Neutra (valor);
	 * 4 - Margem Valor Agregado (%);
	 * 5 -
	 * Pauta (valor).
	 */
	const MODALIDADE_TABELADO = 'tabelado';
	const MODALIDADE_NEGATIVO = 'negativo';
	const MODALIDADE_POSITIVO = 'positivo';
	const MODALIDADE_NEUTRO = 'neutro';
	const MODALIDADE_AGREGADO = 'agregado';
	const MODALIDADE_PAUTA = 'pauta';

	private $origem;
	private $modalidade;
	private $margem;
	private $reducao;

	public function __construct($parcial = array()) {
		parent::__construct($parcial);
		$this->setTributacao('30');
		$this->setGrupo(self::GRUPO_ICMS);
	}

	/**
	 * origem da mercadoria: 0 - Nacional
	 * 1 - Estrangeira - Importação direta
	 * 
	 * 2 - Estrangeira - Adquirida no mercado interno
	 */
	public function getOrigem($normalize = false) {
		if(!$normalize)
			return $this->origem;
		switch ($this->origem) {
			case self::ORIGEM_NACIONAL:
				return '0';
			case self::ORIGEM_ESTRANGEIRA:
				return '1';
			case self::ORIGEM_INTERNO:
				return '2';
		}
		return $this->origem;
	}

	public function setOrigem($origem) {
		$this->origem = $origem;
		return $this;
	}

	/**
	 * Modalidade de determinação da BC do ICMS ST:
	 * 0 – Preço tabelado ou
	 * máximo  sugerido;
	 * 1 - Lista Negativa (valor);
	 * 2 - Lista Positiva
	 * (valor);
	 * 3 - Lista Neutra (valor);
	 * 4 - Margem Valor Agregado (%);
	 * 5 -
	 * Pauta (valor).
	 */
	public function getModalidade($normalize = false) {
		if(!$normalize)
			return $this->modalidade;
		switch ($this->modalidade) {
			case self::MODALIDADE_TABELADO:
				return '0';
			case self::MODALIDADE_NEGATIVO:
				return '1';
			case self::MODALIDADE_POSITIVO:
				return '2';
			case self::MODALIDADE_NEUTRO:
				return '3';
			case self::MODALIDADE_AGREGADO:
				return '4';
			case self::MODALIDADE_PAUTA:
				return '5';
		}
		return $this->modalidade;
	}

	public function setModalidade($modalidade) {
		$this->modalidade = $modalidade;
		return $this;
	}

	public function getMargem($normalize = false) {
		if(!$normalize)
			return $this->margem;
		return Util::toFloat($this->margem);
	}

	public function setMargem($margem) {
		$this->margem = $margem;
		return $this;
	}

	public function getReducao($normalize = false) {
		if(!$normalize)
			return $this->reducao;
		return Util::toFloat($this->reducao);
	}

	public function setReducao($reducao) {
		$this->reducao = $reducao;
		return $this;
	}

	public function toArray() {
		$parcial = parent::toArray();
		$parcial['origem'] = $this->getOrigem();
		$parcial['modalidade'] = $this->getModalidade();
		$parcial['margem'] = $this->getMargem();
		$parcial['reducao'] = $this->getReducao();
		return $parcial;
	}

	public function fromArray($parcial = array()) {
		if($parcial instanceof Parcial)
			$parcial = $parcial->toArray();
		else if(!is_array($parcial))
			return $this;
		parent::fromArray($parcial);
		$this->setOrigem($parcial['origem']);
		if(is_null($this->getOrigem()))
			$this->setOrigem(self::ORIGEM_NACIONAL);
		$this->setModalidade($parcial['modalidade']);
		$this->setMargem($parcial['margem']);
		$this->setReducao($parcial['reducao']);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'IMCS30':$name);
		$element->appendChild($dom->createElement('orig', $this->getOrigem(true)));
		$element->appendChild($dom->createElement('CST', $this->getTributacao(true)));
		$element->appendChild($dom->createElement('modBCST', $this->getModalidade(true)));
		$element->appendChild($dom->createElement('pMVAST', $this->getMargem(true)));
		$element->appendChild($dom->createElement('pRedBCST', $this->getReducao(true)));
		$element->appendChild($dom->createElement('vBCST', $this->getBase(true)));
		$element->appendChild($dom->createElement('pICMSST', $this->getAliquota(true)));
		$element->appendChild($dom->createElement('vICMSST', $this->getValor(true)));
		return $element;
	}

}