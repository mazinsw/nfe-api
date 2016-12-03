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

abstract class Configuracao {

	private $banco;
	private $emitente;
	private $evento;
	private $chave_publica;
	private $chave_privada;

	public function __construct($configuracao = array()) {
		$this->fromArray($configuracao);
	}

	public function getBanco() {
		return $this->banco;
	}

	public function setBanco($banco) {
		$this->banco = $banco;
		return $this;
	}

	/**
	 * Emitente da nota fiscal
	 */
	public function getEmitente() {
		return $this->emitente;
	}

	public function setEmitente($emitente) {
		$this->emitente = $emitente;
		return $this;
	}

	public function getEvento() {
		return $this->evento;
	}

	public function setEvento($evento) {
		$this->evento = $evento;
		return $this;
	}

	abstract public function getChavePublica();

	abstract public function getChavePrivada();

	public function toArray() {
		$configuracao = array();
		$configuracao['banco'] = $this->getBanco();
		$configuracao['emitente'] = $this->getEmitente();
		$configuracao['evento'] = $this->getEvento();
		return $configuracao;
	}

	public function fromArray($configuracao = array()) {
		if($configuracao instanceof Configuracao)
			$configuracao = $configuracao->toArray();
		else if(!is_array($configuracao))
			return $this;
		$this->setBanco($configuracao['banco']);
		if(is_null($this->getEmitente()))
			$this->setEmitente(new Emitente());
		if(is_null($this->getBanco()))
			$this->setBanco(new \BD\Estatico());
		$this->setEvento($configuracao['evento']);
		return $this;
	}

}