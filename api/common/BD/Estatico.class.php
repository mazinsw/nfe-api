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
use IBPT;
use Util;

class Estatico extends Banco {

	private $ibpt;
	private $uf_codes;
	private $mun_codes;

	public function __construct($estatico = array()) {
		parent::__construct($estatico);
		$this->load();
	}

	public function load() {
		$json = file_get_contents(dirname(dirname(dirname(__FILE__))) . '/data/uf_ibge_code.json');
		$this->uf_codes = json_decode($json, true);
		$json = file_get_contents(dirname(dirname(dirname(__FILE__))) . '/data/municipio_ibge_code.json');
		$this->mun_codes = json_decode($json, true);
	}

	public function getIBPT() {
		return $this->ibpt;
	}

	public function setIBPT($ibpt) {
		$this->ibpt = $ibpt;
		return $this;
	}

	/**
	 * Obtém o código IBGE do estado
	 */
	public function getCodigoEstado($uf) {
		return intval($this->uf_codes['estados'][strtoupper($uf)]);
	}

	/**
	 * Obtém a aliquota do imposto de acordo com o tipo
	 */
	public function getImpostoAliquota($ncm, $uf, $ex = null) {
		return $this->getIBPT()->getImposto($ncm, $uf, $ex);
	}

	/**
	 * Obtém o código IBGE do município
	 */
	public function getCodigoMunicipio($municipio, $uf) {
		$array = $this->mun_codes['municipios'][strtoupper($uf)];
		$elem = array('nome' => $municipio);
		$o = Util::binarySearch($elem, $array, function($o1, $o2) {
			$n1 = Util::removeAccent($o1['nome']);
			$n2 = Util::removeAccent($o2['nome']);
			return strcasecmp($n1, $n2);
		});
		if($o === false)
			return false;
		return $o['codigo'];
	}

	/**
	 * Obtém as notas pendentes de envio
	 */
	public function getNotasPendentes($inicio = null, $quantidade = null) {
		return array(); // TODO implementar
	}

	public function toArray() {
		$estatico = parent::toArray();
		$estatico['ibpt'] = $this->getIBPT();
		return $estatico;
	}

	public function fromArray($estatico = array()) {
		if($estatico instanceof Estatico)
			$estatico = $estatico->toArray();
		else if(!is_array($estatico))
			return $this;
		parent::fromArray($estatico);
		$this->setIBPT($estatico['ibpt']);
		if(is_null($this->getIBPT()))
			$this->setIBPT(new IBPT());
		return $this;
	}

}