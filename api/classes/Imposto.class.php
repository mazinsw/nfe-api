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
 * Classe base dos impostos
 */
abstract class Imposto implements NodeInterface {

	/**
	 * Tipo de imposto
	 */
	const TIPO_IMPORTADO = 'importado';
	const TIPO_NACIONAL = 'nacional';
	const TIPO_ESTADUAL = 'estadual';
	const TIPO_MUNICIPAL = 'municipal';

	/**
	 * Grupo do imposto
	 */
	const GRUPO_ICMS = 'icms';
	const GRUPO_PIS = 'pis';
	const GRUPO_COFINS = 'cofins';
	const GRUPO_IPI = 'ipi';
	const GRUPO_II = 'ii';
	const GRUPO_PISST = 'pisst';

	private $tipo;
	private $grupo;
	private $tributacao;
	private $aliquota;
	private $base;

	public function __construct($imposto = array()) {
		$this->fromArray($imposto);
	}

	/**
	 * Tipo de imposto
	 */
	public function getTipo($normalize = false) {
		if(!$normalize)
			return $this->tipo;
		return $this->tipo;
	}

	public function setTipo($tipo) {
		$this->tipo = $tipo;
		return $this;
	}

	/**
	 * Grupo do imposto
	 */
	public function getGrupo($normalize = false) {
		if(!$normalize)
			return $this->grupo;
		switch ($this->grupo) {
			case self::GRUPO_ICMS:
				return 'ICMS';
			case self::GRUPO_PIS:
				return 'PIS';
			case self::GRUPO_COFINS:
				return 'COFINS';
			case self::GRUPO_IPI:
				return 'IPI';
			case self::GRUPO_II:
				return 'II';
			case self::GRUPO_PISST:
				return 'PISST';
		}
		return $this->grupo;
	}

	public function setGrupo($grupo) {
		$this->grupo = $grupo;
		return $this;
	}

	/**
	 * Código da situação tributária
	 */
	public function getTributacao($normalize = false) {
		if(!$normalize)
			return $this->tributacao;
		return $this->tributacao;
	}

	public function setTributacao($tributacao) {
		$this->tributacao = $tributacao;
		return $this;
	}

	/**
	 * Porcentagem do imposto
	 */
	public function getAliquota($normalize = false) {
		if(!$normalize)
			return $this->aliquota;
		return Util::toFloat($this->aliquota);
	}

	public function setAliquota($aliquota) {
		$this->aliquota = $aliquota;
		return $this;
	}

	/**
	 * Valor base para cálculo do imposto
	 */
	public function getBase($normalize = false) {
		if(!$normalize)
			return $this->base;
		return Util::toCurrency($this->base);
	}

	public function setBase($base) {
		$this->base = $base;
		return $this;
	}

	/**
	 * Calcula o valor do imposto com base na aliquota e valor base
	 */
	public function getValor($normalize = false) {
		if(!$normalize)
			return ($this->getBase() * $this->getAliquota()) / 100.0;
		return Util::toCurrency($this->getValor());
	}

	/**
	 * Obtém o valor total do imposto
	 */
	public function getTotal($normalize = false) {
		return $this->getValor($normalize);
	}

	public function toArray() {
		$imposto = array();
		$imposto['tipo'] = $this->getTipo();
		$imposto['grupo'] = $this->getGrupo();
		$imposto['tributacao'] = $this->getTributacao();
		$imposto['aliquota'] = $this->getAliquota();
		$imposto['base'] = $this->getBase();
		$imposto['valor'] = $this->getValor();
		return $imposto;
	}

	public function fromArray($imposto = array()) {
		if($imposto instanceof Imposto)
			$imposto = $imposto->toArray();
		else if(!is_array($imposto))
			return $this;
		$this->setTipo($imposto['tipo']);
		$this->setGrupo($imposto['grupo']);
		$this->setTributacao($imposto['tributacao']);
		$this->setAliquota($imposto['aliquota']);
		$this->setBase($imposto['base']);
		return $this;
	}

}