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
use Imposto;
use DOMDocument;

class Isento extends Imposto {

	/**
	 * Código de Situação Tributária do COFINS:
	 * 04 - Operação Tributável -
	 * Tributação Monofásica - (Alíquota Zero);
	 * 05 - Operação Tributável
	 * (ST);
	 * 06 - Operação Tributável - Alíquota Zero;
	 * 07 - Operação Isenta da
	 * contribuição;
	 * 08 - Operação Sem Incidência da contribuição;
	 * 09 -
	 * Operação com suspensão da contribuição;
	 */
	const TRIBUTACAO_MONOFASICA = 'monofasica';
	const TRIBUTACAO_ST = 'st';
	const TRIBUTACAO_ZERO = 'zero';
	const TRIBUTACAO_ISENTA = 'isenta';
	const TRIBUTACAO_INCIDENCIA = 'incidencia';
	const TRIBUTACAO_SUSPENSAO = 'suspensao';

	public function __construct($cofins = array()) {
		parent::__construct($cofins);
		$this->setGrupo(self::GRUPO_COFINS);
		$this->setBase(0.00);
		$this->setAliquota(0.0);
	}

	/**
	 * Código de Situação Tributária do COFINS:
	 * 04 - Operação Tributável -
	 * Tributação Monofásica - (Alíquota Zero);
	 * 05 - Operação Tributável
	 * (ST);
	 * 06 - Operação Tributável - Alíquota Zero;
	 * 07 - Operação Isenta da
	 * contribuição;
	 * 08 - Operação Sem Incidência da contribuição;
	 * 09 -
	 * Operação com suspensão da contribuição;
	 */
	public function getTributacao($normalize = false) {
		if(!$normalize)
			return parent::getTributacao();
		switch (parent::getTributacao()) {
			case self::TRIBUTACAO_MONOFASICA:
				return '04';
			case self::TRIBUTACAO_ST:
				return '05';
			case self::TRIBUTACAO_ZERO:
				return '06';
			case self::TRIBUTACAO_ISENTA:
				return '07';
			case self::TRIBUTACAO_INCIDENCIA:
				return '08';
			case self::TRIBUTACAO_SUSPENSAO:
				return '09';
		}
		return parent::getTributacao($normalize);
	}

	public function toArray() {
		$cofins = parent::toArray();
		return $cofins;
	}

	public function fromArray($cofins = array()) {
		if($cofins instanceof Isento)
			$cofins = $cofins->toArray();
		else if(!is_array($cofins))
			return $this;
		parent::fromArray($cofins);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'COFINSNT':$name);
		$element->appendChild($dom->createElement('CST', $this->getTributacao(true)));
		return $element;
	}

}