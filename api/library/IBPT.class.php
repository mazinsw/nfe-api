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

class IBPT {

	private $tabela;

	public function __construct() {
		$this->tabela = array();
	}

	private function load($uf) {
		if(isset($this->tabela[$uf]))
			return $this->tabela[$uf];
		$file = dirname(dirname(__FILE__)) . '/data/IBPT/'.$uf.'.json';
		if(!file_exists($file))
			return false;
		$content = file_get_contents($file);
		if($content === false)
			return false;
		$json = json_decode($content, true);
		$array = $json['IBPT'][$uf];
		$this->tabela[$uf] = $array;
		return $array;
	}

	public function getImposto($ncm, $uf, $ex) {
		$uf = strtoupper($uf);
		$uf = preg_replace('/[^A-Z]/', '', $uf);
		$array = $this->load($uf);
		if($array === false)
			return false;
		$key = $ncm.'.'.sprintf('%02s', $ex);
		$o = $array[$key];
		if(is_null($o))
			return false;
		return $o;
	}

}