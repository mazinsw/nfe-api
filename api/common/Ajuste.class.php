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
	private $pasta_xml_inutilizado;
	private $pasta_xml_cancelado;
	private $pasta_xml_pendente;
	private $pasta_xml_denegado;
	private $pasta_xml_rejeitado;
	private $pasta_xml_autorizado;
	private $pasta_xml_processamento;
	private $pasta_xml_assinado;
	private $token;
	private $csc;
	private $token_ibpt;

	public function __construct($ajuste = array()) {
		parent::__construct($ajuste);
		$this->setEvento($this);
		$this->setArquivoChavePublica(dirname(dirname(dirname(__FILE__))) . '/tests/cert/public.pem');
		$this->setArquivoChavePrivada(dirname(dirname(dirname(__FILE__))) . '/tests/cert/private.pem');
		$this->setPastaXmlInutilizado(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/inutilizado');
		$this->setPastaXmlCancelado(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/cancelado');
		$this->setPastaXmlPendente(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/pendente');
		$this->setPastaXmlDenegado(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/denegado');
		$this->setPastaXmlRejeitado(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/rejeitado');
		$this->setPastaXmlAutorizado(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/autorizado');
		$this->setPastaXmlProcessamento(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/processamento');
		$this->setPastaXmlAssinado(dirname(dirname(dirname(__FILE__))) . '/site/xml/{ambiente}/assinado');
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
		if(file_exists($arquivo_chave_publica))
			$this->setChavePublica(file_get_contents($arquivo_chave_publica));
		return $this;
	}

	public function getArquivoChavePrivada() {
		return $this->arquivo_chave_privada;
	}

	public function setArquivoChavePrivada($arquivo_chave_privada) {
		$this->arquivo_chave_privada = $arquivo_chave_privada;
		if(file_exists($arquivo_chave_privada))
			$this->setChavePrivada(file_get_contents($arquivo_chave_privada));
		return $this;
	}

	/**
	 * Pasta onde ficam os XML das inutilizações de números de notas
	 */
	public function getPastaXmlInutilizado($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_inutilizado;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_inutilizado);
	}

	public function setPastaXmlInutilizado($pasta_xml_inutilizado) {
		$this->pasta_xml_inutilizado = $pasta_xml_inutilizado;
		return $this;
	}

	/**
	 * Pasta onde ficam os XML das notas após serem aceitas e depois canceladas
	 */
	public function getPastaXmlCancelado($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_cancelado;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_cancelado);
	}

	public function setPastaXmlCancelado($pasta_xml_cancelado) {
		$this->pasta_xml_cancelado = $pasta_xml_cancelado;
		return $this;
	}

	/**
	 * Pasta onde ficam os XML das notas pendentes
	 */
	public function getPastaXmlPendente($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_pendente;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_pendente);
	}

	public function setPastaXmlPendente($pasta_xml_pendente) {
		$this->pasta_xml_pendente = $pasta_xml_pendente;
		return $this;
	}

	/**
	 * Pasta onde ficam os XMLs após enviados e denegados
	 */
	public function getPastaXmlDenegado($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_denegado;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_denegado);
	}

	public function setPastaXmlDenegado($pasta_xml_denegado) {
		$this->pasta_xml_denegado = $pasta_xml_denegado;
		return $this;
	}

	/**
	 * Pasta onde ficam os XML das notas após serem enviadas e rejeitadas
	 */
	public function getPastaXmlRejeitado($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_rejeitado;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_rejeitado);
	}

	public function setPastaXmlRejeitado($pasta_xml_rejeitado) {
		$this->pasta_xml_rejeitado = $pasta_xml_rejeitado;
		return $this;
	}

	/**
	 * Pasta onde ficam os XML das notas após serem enviados e aceitos pela
	 * SEFAZ
	 */
	public function getPastaXmlAutorizado($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_autorizado;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_autorizado);
	}

	public function setPastaXmlAutorizado($pasta_xml_autorizado) {
		$this->pasta_xml_autorizado = $pasta_xml_autorizado;
		return $this;
	}

	/**
	 * Pasta onde ficam os XMLs em processamento de retorno de autorização
	 */
	public function getPastaXmlProcessamento($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_processamento;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_processamento);
	}

	public function setPastaXmlProcessamento($pasta_xml_processamento) {
		$this->pasta_xml_processamento = $pasta_xml_processamento;
		return $this;
	}

	/**
	 * Pasta onde ficam os XMLs após assinado e antes de serem enviados
	 */
	public function getPastaXmlAssinado($ambiente = null) {
		if(is_null($ambiente))
			return $this->pasta_xml_assinado;
		return str_replace('{ambiente}', $ambiente, $this->pasta_xml_assinado);
	}

	public function setPastaXmlAssinado($pasta_xml_assinado) {
		$this->pasta_xml_assinado = $pasta_xml_assinado;
		return $this;
	}

	public function getToken() {
		return $this->token;
	}

	public function setToken($token) {
		$this->token = $token;
		return $this;
	}

	public function getCSC() {
		return $this->csc;
	}

	public function setCSC($csc) {
		$this->csc = $csc;
		return $this;
	}

	public function getTokenIBPT() {
		return $this->token_ibpt;
	}

	public function setTokenIBPT($token_ibpt) {
		$this->token_ibpt = $token_ibpt;
		return $this;
	}

	/**
	 * Chamado quando o XML da nota foi gerado
	 */
	public function onNotaGerada(&$nota, &$xml) {
		//echo 'XML gerado!<br>';
	}

	/**
	 * Chamado após o XML da nota ser assinado
	 */
	public function onNotaAssinada(&$nota, &$xml) {
		//echo 'XML assinado!<br>';
		$filename = $this->getPastaXmlAssinado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
		file_put_contents($filename, $xml->saveXML());
		if(!$nota->testar($filename))
			throw new Exception('Falha na assinatura do XML');
	}

	/**
	 * Chamado antes de enviar a nota para a SEFAZ
	 */
	public function onNotaEnviando(&$nota, &$xml) {
		//echo 'Enviando XML...<br>';
	}

	/**
	 * Chamado quando a forma de emissão da nota fiscal muda para normal ou
	 * contigência
	 */
	public function onFormaEmissao(&$nota, $forma) {
		echo 'Forma de emissão alterada para "'.$forma.'" <br>';
	}

	/**
	 * Chamado quando a nota foi enviada e aceita pela SEFAZ
	 */
	public function onNotaEnviada(&$nota, &$xml) {
		//echo 'XML enviado com sucesso!<br>';
		$filename = $this->getPastaXmlAutorizado($nota->getAmbiente()) . '/' . $nota->getID() . '.xml';
		file_put_contents($filename, $xml->saveXML());
	}

	/**
	 * Chamado quando a emissão da nota foi concluída com sucesso independente
	 * da forma de emissão
	 */
	public function onNotaCompleto(&$nota, &$xml) {
		//echo 'XML processado com sucesso!<br>';
	}

	/**
	 * Chamado quando ocorre um erro nas etapas de geração e envio da nota (Não
	 * é chamado quando entra em contigência)
	 */
	public function onNotaErro(&$nota, $e) {
		echo 'Falha no processamento da nota: '.$e->getMessage().'<br>';
		die();
	}

	public function toArray() {
		$ajuste = parent::toArray();
		$ajuste['chave_publica'] = $this->getChavePublica();
		$ajuste['chave_privada'] = $this->getChavePrivada();
		$ajuste['arquivo_chave_publica'] = $this->getArquivoChavePublica();
		$ajuste['arquivo_chave_privada'] = $this->getArquivoChavePrivada();
		$ajuste['pasta_xml_inutilizado'] = $this->getPastaXmlInutilizado();
		$ajuste['pasta_xml_cancelado'] = $this->getPastaXmlCancelado();
		$ajuste['pasta_xml_pendente'] = $this->getPastaXmlPendente();
		$ajuste['pasta_xml_denegado'] = $this->getPastaXmlDenegado();
		$ajuste['pasta_xml_rejeitado'] = $this->getPastaXmlRejeitado();
		$ajuste['pasta_xml_autorizado'] = $this->getPastaXmlAutorizado();
		$ajuste['pasta_xml_processamento'] = $this->getPastaXmlProcessamento();
		$ajuste['pasta_xml_assinado'] = $this->getPastaXmlAssinado();
		$ajuste['token'] = $this->getToken();
		$ajuste['csc'] = $this->getCSC();
		$ajuste['token_ibpt'] = $this->getTokenIBPT();
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
		$this->setPastaXmlInutilizado($ajuste['pasta_xml_inutilizado']);
		$this->setPastaXmlCancelado($ajuste['pasta_xml_cancelado']);
		$this->setPastaXmlPendente($ajuste['pasta_xml_pendente']);
		$this->setPastaXmlDenegado($ajuste['pasta_xml_denegado']);
		$this->setPastaXmlRejeitado($ajuste['pasta_xml_rejeitado']);
		$this->setPastaXmlAutorizado($ajuste['pasta_xml_autorizado']);
		$this->setPastaXmlProcessamento($ajuste['pasta_xml_processamento']);
		$this->setPastaXmlAssinado($ajuste['pasta_xml_assinado']);
		$this->setToken($ajuste['token']);
		$this->setCSC($ajuste['csc']);
		$this->setTokenIBPT($ajuste['token_ibpt']);
		return $this;
	}

}