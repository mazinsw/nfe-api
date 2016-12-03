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
 * Configurações padrão para emissão de nota fiscal
 */
class Ajuste extends Configuracao implements Evento {

	private $chave_publica;
	private $chave_privada;
	private $arquivo_chave_publica;
	private $arquivo_chave_privada;
	private $pasta_xml_final;
	private $pasta_xml_gerado;

	public function __construct($ajuste = array()) {
		parent::__construct($ajuste);
		$this->setEvento($this);
	}

	public function getChavePublica() {
		return $this->chave_publica;
	}

	public function setChavePublica($chave_publica) {
		$this->chave_publica = $chave_publica;
		return $this;
	}

	public function getChavePrivada() {
		return $this->chave_privada;
	}

	public function setChavePrivada($chave_privada) {
		$this->chave_privada = $chave_privada;
		return $this;
	}

	public function getArquivoChavePublica() {
		return $this->arquivo_chave_publica;
	}

	public function setArquivoChavePublica($arquivo_chave_publica) {
		$this->arquivo_chave_publica = $arquivo_chave_publica;
		return $this;
	}

	public function getArquivoChavePrivada() {
		return $this->arquivo_chave_privada;
	}

	public function setArquivoChavePrivada($arquivo_chave_privada) {
		$this->arquivo_chave_privada = $arquivo_chave_privada;
		return $this;
	}

	/**
	 * Pasta onde fica os XML das notas após serem enviadas e aceitas pela
	 * SEFAZ
	 */
	public function getPastaXmlFinal() {
		return $this->pasta_xml_final;
	}

	public function setPastaXmlFinal($pasta_xml_final) {
		$this->pasta_xml_final = $pasta_xml_final;
		return $this;
	}

	/**
	 * Pasta onde ficam os XMLs após gerado e antes de serem aceitos
	 */
	public function getPastaXmlGerado() {
		return $this->pasta_xml_gerado;
	}

	public function setPastaXmlGerado($pasta_xml_gerado) {
		$this->pasta_xml_gerado = $pasta_xml_gerado;
		return $this;
	}

	/**
	 * Chamado quando o XML da nota foi gerado
	 */
	public function onNotaGerada(&$nota, &$xml) {

	}

	/**
	 * Chamado após o XML da nota ser assinado
	 */
	public function onNotaAssinada(&$nota, &$xml) {

	}

	/**
	 * Chamado antes de enviar a nota para a SEFAZ
	 */
	public function onNotaEnviando(&$nota, &$xml) {

	}

	/**
	 * Chamado quando a forma de emissão da nota fiscal muda para normal ou
	 * contigência
	 */
	public function onFormaEmissao(&$nota, $forma) {

	}

	/**
	 * Chamado quando a nota foi enviada e aceita pela SEFAZ (Não é chamado
	 * quando em contigência)
	 */
	public function onNotaEnviada(&$nota, &$xml) {

	}

	/**
	 * Chamado quando a emissão da nota foi concluída com sucesso independente
	 * da forma de emissão
	 */
	public function onNotaCompleto(&$nota, &$xml) {

	}

	/**
	 * Chamado quando ocorre um erro nas etapas de geração e envio da nota (Não
	 * é chamado quando entra em contigência)
	 */
	public function onNotaErro(&$nota) {
		
	}

	public function toArray() {
		$ajuste = parent::toArray();
		$ajuste['chave_publica'] = $this->getChavePublica();
		$ajuste['chave_privada'] = $this->getChavePrivada();
		$ajuste['arquivo_chave_publica'] = $this->getArquivoChavePublica();
		$ajuste['arquivo_chave_privada'] = $this->getArquivoChavePrivada();
		$ajuste['pasta_xml_final'] = $this->getPastaXmlFinal();
		$ajuste['pasta_xml_gerado'] = $this->getPastaXmlGerado();
		return $ajuste;
	}

	public function fromArray($ajuste = array()) {
		if($ajuste instanceof Ajuste)
			$ajuste = $ajuste->toArray();
		else if(!is_array($ajuste))
			return $this;
		parent::fromArray($ajuste);
		$this->setChavePublica($ajuste['chave_publica']);
		$this->setChavePrivada($ajuste['chave_privada']);
		$this->setArquivoChavePublica($ajuste['arquivo_chave_publica']);
		$this->setArquivoChavePrivada($ajuste['arquivo_chave_privada']);
		$this->setPastaXmlFinal($ajuste['pasta_xml_final']);
		$this->setPastaXmlGerado($ajuste['pasta_xml_gerado']);
		return $this;
	}

}