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

class PagamentoForma {
	const DINHEIRO = 'dinheiro';
	const CHEQUE = 'cheque';
	const CREDITO = 'credito';
	const DEBITO = 'debito';
	const CREDIARIO = 'crediario';
	const ALIMENTACAO = 'alimentacao';
	const REFEICAO = 'refeicao';
	const PRESENTE = 'presente';
	const COMBUSTIVEL = 'combustivel';
	const OUTROS = 'outros';
}

class PagamentoBandeira {
	const VISA = 'visa';
	const MASTERCARD = 'mastercard';
	const AMEX = 'amex';
	const SOROCRED = 'sorocred';
	const OUTROS = 'outros';
}

class Pagamento implements NodeInterface {

	private $forma;
	private $valor;
	private $credenciadora;
	private $autorizacao;
	private $bandeira;

	public function __construct($pagamento = array()) {
		$this->fromArray($pagamento);
	}

	/**
	 * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
	 * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
	 * Presente;13-Vale Combustível;99 - Outros
	 */
	public function getForma($normalize = false) {
		if(!$normalize)
			return $this->forma;
		switch ($this->forma) {
			case PagamentoForma::DINHEIRO:
				return '01';
			case PagamentoForma::CHEQUE:
				return '02';
			case PagamentoForma::CREDITO:
				return '03';
			case PagamentoForma::DEBITO:
				return '04';
			case PagamentoForma::CREDIARIO:
				return '05';
			case PagamentoForma::ALIMENTACAO:
				return '10';
			case PagamentoForma::REFEICAO:
				return '11';
			case PagamentoForma::PRESENTE:
				return '12';
			case PagamentoForma::COMBUSTIVEL:
				return '13';
			case PagamentoForma::OUTROS:
				return '99';
		}
		return $this->forma;
	}

	public function setForma($forma) {
		$this->forma = $forma;
		return $this;
	}

	/**
	 * Valor do Pagamento
	 */
	public function getValor($normalize = false) {
		if(!$normalize)
			return $this->valor;
		return Util::toCurrency($this->valor);
	}

	public function setValor($valor) {
		$this->valor = $valor;
		return $this;
	}

	/**
	 * CNPJ da credenciadora de cartão de crédito/débito
	 */
	public function getCredenciadora($normalize = false) {
		if(!$normalize)
			return $this->credenciadora;
		return $this->credenciadora;
	}

	public function setCredenciadora($credenciadora) {
		$this->credenciadora = $credenciadora;
		return $this;
	}

	/**
	 * Número de autorização da operação cartão de crédito/débito
	 */
	public function getAutorizacao($normalize = false) {
		if(!$normalize)
			return $this->autorizacao;
		return $this->autorizacao;
	}

	public function setAutorizacao($autorizacao) {
		$this->autorizacao = $autorizacao;
		return $this;
	}

	/**
	 * Bandeira da operadora de cartão de crédito/débito:01–Visa;
	 * 02–Mastercard; 03–American Express; 04–Sorocred; 99–Outros
	 */
	public function getBandeira($normalize = false) {
		if(!$normalize)
			return $this->bandeira;
		switch ($this->bandeira) {
			case PagamentoBandeira::VISA:
				return '01';
			case PagamentoBandeira::MASTERCARD:
				return '02';
			case PagamentoBandeira::AMEX:
				return '03';
			case PagamentoBandeira::SOROCRED:
				return '04';
			case PagamentoBandeira::OUTROS:
				return '99';
		}
		return $this->bandeira;
	}

	public function setBandeira($bandeira) {
		$this->bandeira = $bandeira;
		return $this;
	}

	public function toArray() {
		$pagamento = array();
		$pagamento['forma'] = $this->getForma();
		$pagamento['valor'] = $this->getValor();
		$pagamento['credenciadora'] = $this->getCredenciadora();
		$pagamento['autorizacao'] = $this->getAutorizacao();
		$pagamento['bandeira'] = $this->getBandeira();
		return $pagamento;
	}

	public function fromArray($pagamento = array()) {
		if($pagamento instanceof Pagamento)
			$pagamento = $pagamento->toArray();
		else if(!is_array($pagamento))
			return $this;
		$this->setForma($pagamento['forma']);
		$this->setValor($pagamento['valor']);
		$this->setCredenciadora($pagamento['credenciadora']);
		$this->setAutorizacao($pagamento['autorizacao']);
		$this->setBandeira($pagamento['bandeira']);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'pag':$name);
		$element->appendChild($dom->createElement('tPag', $this->getForma(true)));
		$element->appendChild($dom->createElement('vPag', $this->getValor(true)));
		if($this->getForma() != PagamentoForma::CREDITO && $this->getForma() != PagamentoForma::DEBITO)
			return $element;
		$cartao = $dom->createElement('card');
		$cartao->appendChild($dom->createElement('CNPJ', $this->getCredenciadora(true)));
		$cartao->appendChild($dom->createElement('tBand', $this->getBandeira(true)));
		$cartao->appendChild($dom->createElement('cAut', $this->getAutorizacao(true)));
		$element->appendChild($cartao);
		return $element;
	}

}
