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
namespace BD;

abstract class Banco {

	public function __construct($banco = array()) {
		$this->fromArray($banco);
	}

	/**
	 * Obtém o código IBGE do estado
	 */
	abstract public function getCodigoEstado($uf);

	/**
	 * Obtém a aliquota do imposto de acordo com o tipo
	 */
	abstract public function getImpostoAliquota($ncm, $uf, $ex = null);

	/**
	 * Obtém o código IBGE do município
	 */
	abstract public function getCodigoMunicipio($municipio, $uf);

	/**
	 * Obtém as notas pendentes de envio
	 */
	abstract public function getNotasPendentes($inicio = null, $quantidade = null);


	public function toArray() {
		$banco = array();
		return $banco;
	}

	public function fromArray($banco = array()) {
		if($banco instanceof Banco)
			$banco = $banco->toArray();
		else if(!is_array($banco))
			return $this;
		return $this;
	}
}