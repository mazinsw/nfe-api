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
namespace NF;

class Inutilizacao implements NodeInterface {

	private $id;
	private $ambiente;
	private $uf;
	private $ano;
	private $cnpj;
	private $modelo;
	private $serie;
	private $inicio;
	private $final;
	private $justificativa;

	public function __construct($inutilizacao = array()) {
		$this->fromArray($inutilizacao);
	}

	/**
	 * Formado por:
	 * ID = Literal
	 * 43 = Código Estado
	 * 15 = Ano
	 * 
	 * 00000000000000 = CNPJ
	 * 55 = Modelo
	 * 001 = Série
	 * 000000411 =
	 * Número Inicial
	 * 000000411 = Número Final
	 */
	public function getID($normalize = false) {
		if(!$normalize)
			return $this->id;
		return $this->id;
	}

	public function setID($id) {
		$this->id = $id;
		return $this;
	}

	public function getAmbiente() {
		return $this->ambiente;
	}

	public function setAmbiente($ambiente) {
		$this->ambiente = $ambiente;
		return $this;
	}

	public function getUF() {
		return $this->uf;
	}

	public function setUF($uf) {
		$this->uf = $uf;
		return $this;
	}

	public function getAno() {
		return $this->ano;
	}

	public function setAno($ano) {
		$this->ano = $ano;
		return $this;
	}

	public function getCNPJ() {
		return $this->cnpj;
	}

	public function setCNPJ($cnpj) {
		$this->cnpj = $cnpj;
		return $this;
	}

	public function getModelo() {
		return $this->modelo;
	}

	public function setModelo($modelo) {
		$this->modelo = $modelo;
		return $this;
	}

	public function getSerie() {
		return $this->serie;
	}

	public function setSerie($serie) {
		$this->serie = $serie;
		return $this;
	}

	public function getInicio() {
		return $this->inicio;
	}

	public function setInicio($inicio) {
		$this->inicio = $inicio;
		return $this;
	}

	public function getFinal() {
		return $this->final;
	}

	public function setFinal($final) {
		$this->final = $final;
		return $this;
	}

	public function getJustificativa() {
		return $this->justificativa;
	}

	public function setJustificativa($justificativa) {
		$this->justificativa = $justificativa;
		return $this;
	}

	public function toArray() {
		$inutilizacao = array();
		$inutilizacao['id'] = $this->getID();
		$inutilizacao['ambiente'] = $this->getAmbiente();
		$inutilizacao['uf'] = $this->getUF();
		$inutilizacao['ano'] = $this->getAno();
		$inutilizacao['cnpj'] = $this->getCNPJ();
		$inutilizacao['modelo'] = $this->getModelo();
		$inutilizacao['serie'] = $this->getSerie();
		$inutilizacao['inicio'] = $this->getInicio();
		$inutilizacao['final'] = $this->getFinal();
		$inutilizacao['justificativa'] = $this->getJustificativa();
		return $inutilizacao;
	}

	public function fromArray($inutilizacao = array()) {
		if($inutilizacao instanceof Inutilizacao)
			$inutilizacao = $inutilizacao->toArray();
		else if(!is_array($inutilizacao))
			return $this;
		$this->setID($inutilizacao['id']);
		$this->setAmbiente($inutilizacao['ambiente']);
		$this->setUF($inutilizacao['uf']);
		$this->setAno($inutilizacao['ano']);
		$this->setCNPJ($inutilizacao['cnpj']);
		$this->setModelo($inutilizacao['modelo']);
		$this->setSerie($inutilizacao['serie']);
		$this->setInicio($inutilizacao['inicio']);
		$this->setFinal($inutilizacao['final']);
		$this->setJustificativa($inutilizacao['justificativa']);
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'inutNFe':$name);
		$element->appendChild($dom->createElement('infInut', $this->getID(true)));
		$ambiente = $this->getAmbiente()->getNode();
		$ambiente = $dom->importNode($ambiente, true);
		$element->appendChild($ambiente);
		$uf = $this->getUF()->getNode();
		$uf = $dom->importNode($uf, true);
		$element->appendChild($uf);
		$ano = $this->getAno()->getNode();
		$ano = $dom->importNode($ano, true);
		$element->appendChild($ano);
		$cnpj = $this->getCNPJ()->getNode();
		$cnpj = $dom->importNode($cnpj, true);
		$element->appendChild($cnpj);
		$modelo = $this->getModelo()->getNode();
		$modelo = $dom->importNode($modelo, true);
		$element->appendChild($modelo);
		$serie = $this->getSerie()->getNode();
		$serie = $dom->importNode($serie, true);
		$element->appendChild($serie);
		$inicio = $this->getInicio()->getNode();
		$inicio = $dom->importNode($inicio, true);
		$element->appendChild($inicio);
		$final = $this->getFinal()->getNode();
		$final = $dom->importNode($final, true);
		$element->appendChild($final);
		$justificativa = $this->getJustificativa()->getNode();
		$justificativa = $dom->importNode($justificativa, true);
		$element->appendChild($justificativa);
		return $element;
	}

}