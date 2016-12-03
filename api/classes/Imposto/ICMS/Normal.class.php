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

/**
 * Classe base do ICMS normal, estende de Imposto
 */
class Normal extends Imposto {

	/**
	 * origem da mercadoria: 0 - Nacional
	 * 1 - Estrangeira - Importação direta
	 * 
	 * 2 - Estrangeira - Adquirida no mercado interno
	 */
	const ORIGEM_NACIONAL = 'nacional';
	const ORIGEM_ESTRANGEIRA = 'estrangeira';
	const ORIGEM_INTERNO = 'interno';

	const MODALIDADE_AGREGADO = 'agregado';
	const MODALIDADE_PAUTA = 'pauta';
	const MODALIDADE_TABELADO = 'tabelado';
	const MODALIDADE_OPERACAO = 'operacao';

	private $origem;
	private $modalidade;

	public function __construct($normal = array()) {
		parent::__construct($normal);
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

	public function getModalidade($normalize = false) {
		if(!$normalize)
			return $this->modalidade;
		switch ($this->modalidade) {
			case self::MODALIDADE_AGREGADO:
				return '0';
			case self::MODALIDADE_PAUTA:
				return '1';
			case self::MODALIDADE_TABELADO:
				return '2';
			case self::MODALIDADE_OPERACAO:
				return '3';
		}
		return $this->modalidade;
	}

	public function setModalidade($modalidade) {
		$this->modalidade = $modalidade;
		return $this;
	}

	public function toArray() {
		$normal = parent::toArray();
		$normal['origem'] = $this->getOrigem();
		$normal['modalidade'] = $this->getModalidade();
		return $normal;
	}

	public function fromArray($normal = array()) {
		if($normal instanceof Normal)
			$normal = $normal->toArray();
		else if(!is_array($normal))
			return $this;
		parent::fromArray($normal);
		$this->setOrigem($normal['origem']);
		if(is_null($this->getOrigem()))
			$this->setOrigem(self::ORIGEM_NACIONAL);
		$this->setModalidade($normal['modalidade']);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'IMCS':$name);
		$element->appendChild($dom->createElement('orig', $this->getOrigem(true)));
		$element->appendChild($dom->createElement('CST', $this->getTributacao(true)));
		$element->appendChild($dom->createElement('modBC', $this->getModalidade(true)));
		$element->appendChild($dom->createElement('vBC', $this->getBase(true)));
		$element->appendChild($dom->createElement('pICMS', $this->getAliquota(true)));
		$element->appendChild($dom->createElement('vICMS', $this->getValor(true)));
		return $element;
	}

}