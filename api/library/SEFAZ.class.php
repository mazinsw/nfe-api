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
 * Classe que envia uma ou mais notas fiscais para os servidores da sefaz
 */
class SEFAZ {
	private $notas;

	public function __construct($sefaz = array()) {
		if(is_array($sefaz)) {
			$this->setNotas($sefaz['notas']);
		}
	}

	public function getNotas() {
		return $this->notas;
	}

	public function setNotas($notas) {
		$this->notas = $notas;
	}

	public function toArray() {
		$sefaz = array();
		$sefaz['notas'] = $this->getNotas();
		return $sefaz;
	}

	private static function validarCampos(&$sefaz) {
		$erros = array();
		if(!is_numeric($sefaz['notas']))
			$erros['notas'] = 'O notas não foi informado';
		if(!empty($erros))
			throw new ValidationException($erros);
	}

	private static function initSearch() {
		return   DB::$pdo->from('SEFAZ');
	}

	public static function getTodas($inicio = null, $quantidade = null) {
		$query = self::initSearch();
		if(!is_null($inicio) && !is_null($quantidade)) {
			$query = $query->limit($quantidade)->offset($inicio);
		}
		$_sefazs = $query->fetchAll();
		$sefazs = array();
		foreach($_sefazs as $sefaz)
			$sefazs[] = new SEFAZ($sefaz);
		return $sefazs;
	}

	public static function getCount() {
		$query = self::initSearch();
		return $query->count();
	}

}
