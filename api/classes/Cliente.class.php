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
 * Cliente pessoa física ou jurídica que está comprando os produtos e irá
 * receber a nota fiscal
 */
class Cliente extends Pessoa {
	
	/**
	 * Indicador da IE do destinatário:
	 * 1 – Contribuinte ICMSpagamento à
	 * vista;
	 * 2 – Contribuinte isento de inscrição;
	 * 9 – Não Contribuinte
	 */
	const INDICADOR_PAGAMENTO = 'pagamento';
	const INDICADOR_ISENTO = 'isento';
	const INDICADOR_NENHUM = 'nenhum';

	private $nome;
	private $cpf;
	private $email;
	private $indicador;

	public function __construct($cliente = array()) {
		parent::__construct($cliente);
	}

	/**
	 * Número identificador do cliente
	 */
	public function getID($normalize = false) {
		if(!is_null($this->getCNPJ()))
			return $this->getCNPJ($normalize);
		return $this->getCPF($normalize);
	}

	/**
	 * Nome do destinatário
	 */
	public function getNome($normalize = false) {
		if(!$normalize)
			return $this->nome;
		return $this->nome;
	}

	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}

	public function getCPF($normalize = false) {
		if(!$normalize)
			return $this->cpf;
		return $this->cpf;
	}

	public function setCPF($cpf) {
		$this->cpf = $cpf;
		return $this;
	}

	/**
	 * Informar o e-mail do destinatário. O campo pode ser utilizado para
	 * informar o e-mail de recepção da NF-e indicada pelo destinatário
	 */
	public function getEmail($normalize = false) {
		if(!$normalize)
			return $this->email;
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * Indicador da IE do destinatário:
	 * 1 – Contribuinte ICMSpagamento à
	 * vista;
	 * 2 – Contribuinte isento de inscrição;
	 * 9 – Não Contribuinte
	 */
	public function getIndicador($normalize = false) {
		if(!$normalize)
			return $this->indicador;
		switch ($this->indicador) {
			case self::INDICADOR_PAGAMENTO:
				return '1';
			case self::INDICADOR_ISENTO:
				return '2';
			case self::INDICADOR_NENHUM:
				return '9';
		}
		return $this->indicador;
	}

	public function setIndicador($indicador) {
		$this->indicador = $indicador;
		return $this;
	}

	public function toArray() {
		$cliente = parent::toArray();
		$cliente['nome'] = $this->getNome();
		$cliente['cpf'] = $this->getCPF();
		$cliente['email'] = $this->getEmail();
		$cliente['indicador'] = $this->getIndicador();
		return $cliente;
	}

	public function fromArray($cliente = array()) {
		if($cliente instanceof Cliente)
			$cliente = $cliente->toArray();
		else if(!is_array($cliente))
			return $this;
		parent::fromArray($cliente);
		$this->setNome($cliente['nome']);
		$this->setCPF($cliente['cpf']);
		$this->setEmail($cliente['email']);
		$this->setIndicador($cliente['indicador']);
		if(is_null($this->getIndicador()))
			$this->setIndicador(self::INDICADOR_NENHUM);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'dest':$name);
		if(!is_null($this->getCNPJ()))
			$element->appendChild($dom->createElement('CNPJ', $this->getCNPJ(true)));
		else
			$element->appendChild($dom->createElement('CPF', $this->getCPF(true)));
		if(!is_null($this->getCNPJ()))
			$element->appendChild($dom->createElement('xNome', $this->getRazaoSocial(true)));
		else
			$element->appendChild($dom->createElement('xNome', $this->getNome(true)));
		if(!is_null($this->getEndereco())) {
			$endereco = $this->getEndereco()->getNode('enderDest');
			$endereco = $dom->importNode($endereco, true);
			$endereco->appendChild($dom->createElement('fone', $this->getTelefone(true)));
			$element->appendChild($endereco);
		}
		$element->appendChild($dom->createElement('indIEDest', $this->getIndicador(true)));
		if(!is_null($this->getCNPJ())) {
			$element->appendChild($dom->createElement('IE', $this->getIE(true)));
			$element->appendChild($dom->createElement('email', $this->getEmail(true)));
		}
		return $element;
	}

}
