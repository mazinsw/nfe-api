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
 * Classe base para preenchimento de informações de pessoas físicas e
 * empresas
 */
abstract class Pessoa implements NodeInterface {

	private $razao_social;
	private $cnpj;
	private $ie;
	private $im;
	private $endereco;
	private $telefone;

	public function __construct($pessoa = array()) {
		$this->fromArray($pessoa);
	}

	/**
	 * Razão Social ou Nome
	 */
	public function getRazaoSocial($normalize = false) {
		if(!$normalize)
			return $this->razao_social;
		return $this->razao_social;
	}

	public function setRazaoSocial($razao_social) {
		$this->razao_social = $razao_social;
		return $this;
	}

	/**
	 * Identificador da pessoa na receita
	 */
	public function getCNPJ($normalize = false) {
		if(!$normalize)
			return $this->cnpj;
		return $this->cnpj;
	}

	public function setCNPJ($cnpj) {
		$this->cnpj = $cnpj;
		return $this;
	}

	/**
	 * Inscrição Estadual
	 */
	public function getIE($normalize = false) {
		if(!$normalize)
			return $this->ie;
		return $this->ie;
	}

	public function setIE($ie) {
		$this->ie = $ie;
		return $this;
	}

	/**
	 * Inscrição Municipal
	 */
	public function getIM($normalize = false) {
		if(!$normalize)
			return $this->im;
		return $this->im;
	}

	public function setIM($im) {
		$this->im = $im;
		return $this;
	}

	/**
	 * Dados do endereço
	 */
	public function getEndereco() {
		return $this->endereco;
	}

	public function setEndereco($endereco) {
		$this->endereco = $endereco;
		return $this;
	}

	public function getTelefone($normalize = false) {
		if(!$normalize)
			return $this->telefone;
		return $this->telefone;
	}

	public function setTelefone($telefone) {
		$this->telefone = $telefone;
		return $this;
	}

	public function toArray() {
		$pessoa = array();
		$pessoa['razao_social'] = $this->getRazaoSocial();
		$pessoa['cnpj'] = $this->getCNPJ();
		$pessoa['ie'] = $this->getIE();
		$pessoa['im'] = $this->getIM();
		$pessoa['endereco'] = $this->getEndereco();
		$pessoa['telefone'] = $this->getTelefone();
		return $pessoa;
	}

	public function fromArray($pessoa = array()) {
		if($pessoa instanceof Pessoa)
			$pessoa = $pessoa->toArray();
		else if(!is_array($pessoa))
			return $this;
		$this->setRazaoSocial($pessoa['razao_social']);
		$this->setCNPJ($pessoa['cnpj']);
		$this->setIE($pessoa['ie']);
		$this->setIM($pessoa['im']);
		$this->setEndereco($pessoa['endereco']);
		$this->setTelefone($pessoa['telefone']);
		return $this;
	}

}