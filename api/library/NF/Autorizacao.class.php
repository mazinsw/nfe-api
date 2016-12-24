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
use NF;
use DOMDocument;
use CurlSoap;
use NodeInterface;
use Log;
use SEFAZ;
use Util;

class Autorizacao implements NodeInterface {

	private $ambiente;
	private $versao;
	private $status;
	private $motivo;
	private $uf;
	private $data_recebimento;

	public function __construct($autorizacao = array()) {
		$this->fromArray($autorizacao);
	}

	/**
	 * Identificação do Ambiente: 1 - Produção, 2 - Homologação
	 */
	public function getAmbiente($normalize = false) {
		if(!$normalize)
			return $this->ambiente;
		switch ($this->ambiente) {
			case NF::AMBIENTE_PRODUCAO:
				return '1';
			case NF::AMBIENTE_HOMOLOGACAO:
				return '2';
		}
		return $this->ambiente;
	}

	public function setAmbiente($ambiente) {
		$this->ambiente = $ambiente;
		return $this;
	}

	public function getVersao($normalize = false) {
		if(!$normalize)
			return $this->versao;
		return $this->versao;
	}

	public function setVersao($versao) {
		$this->versao = $versao;
		return $this;
	}

	public function getStatus($normalize = false) {
		if(!$normalize)
			return $this->status;
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	public function getMotivo($normalize = false) {
		if(!$normalize)
			return $this->motivo;
		return $this->motivo;
	}

	public function setMotivo($motivo) {
		$this->motivo = $motivo;
		return $this;
	}

	public function getUF($normalize = false) {
		if(!$normalize)
			return $this->uf;
		return $this->uf;
	}

	public function setUF($uf) {
		$this->uf = $uf;
		return $this;
	}

	public function getDataRecebimento($normalize = false) {
		if(!$normalize)
			return $this->data_recebimento;
		return Util::toDateTime($this->data_recebimento);
	}

	public function setDataRecebimento($data_recebimento) {
		$this->data_recebimento = $data_recebimento;
		return $this;
	}

	public function toArray() {
		$autorizacao = array();
		$autorizacao['ambiente'] = $this->getAmbiente();
		$autorizacao['versao'] = $this->getVersao();
		$autorizacao['status'] = $this->getStatus();
		$autorizacao['motivo'] = $this->getMotivo();
		$autorizacao['uf'] = $this->getUF();
		$autorizacao['data_recebimento'] = $this->getDataRecebimento();
		return $autorizacao;
	}

	public function fromArray($autorizacao = array()) {
		if($autorizacao instanceof Autorizacao)
			$autorizacao = $autorizacao->toArray();
		else if(!is_array($autorizacao))
			return $this;
		$this->setAmbiente($autorizacao['ambiente']);
		$this->setVersao($autorizacao['versao']);
		$this->setStatus($autorizacao['status']);
		$this->setMotivo($autorizacao['motivo']);
		$this->setUF($autorizacao['uf']);
		$this->setDataRecebimento($autorizacao['data_recebimento']);
		return $this;
	}

	static public function genLote()
	{
		return substr(Util::padText(number_format(microtime(true)*1000000, 0, '', ''), 15), 0, 15);
	}

	private function getNodeHeader(&$nota, &$dom) {
		$estado = $nota->getEmitente()->getEndereco()->getMunicipio()->getEstado();
		$estado->checkCodigos();
		$doh = new DOMDocument('1.0', 'UTF-8');
		$element = $doh->createElement('nfeCabecMsg');
		$element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL.'/wsdl/NfeAutorizacao');
		$element->appendChild($doh->createElement('cUF', $estado->getCodigo(true)));
		$element->appendChild($doh->createElement('versaoDados', NF::VERSAO));
		$doh->appendChild($element);
		return $doh;
	}

	private function getNodeBody(&$nota, &$dom) {
		$dob = new DOMDocument('1.0', 'UTF-8');
		$element = $dob->createElement('nfeDadosMsg');
		$element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL.'/wsdl/NfeAutorizacao');

		$envio = $dob->createElement('enviNFe');
		$envio->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL);
		$versao = $dob->createAttribute('versao');
		$versao->value = NF::VERSAO;
		$envio->appendChild($versao);
		$envio->appendChild($dob->createElement('idLote', self::genLote()));
		$envio->appendChild($dob->createElement('indSinc', 1));
		// Corrige xmlns:default
		// $data = $dob->importNode($dom->documentElement, true);
		// $envio->appendChild($data);
		$envio->appendChild($dob->createElement('NFe', 0)); 
		
		$element->appendChild($envio);
		$dob->appendChild($element);
		// Corrige xmlns:default
		// return $dob;
		$xml = $dob->saveXML();
		$xml = str_replace('<NFe>0</NFe>', $dom->saveXML($dom->documentElement), $xml);
		$dob->loadXML($xml);
		return $dob;
	}

	public function envia(&$nota, &$dom) {
		$config = SEFAZ::getInstance()->getConfiguracao();
		$db = $config->getBanco();
		$estado = $nota->getEmitente()->getEndereco()->getMunicipio()->getEstado();
		$info = $db->getInformacaoServico($nota->getEmissao(), $estado->getUF(), $nota->getModelo(), $nota->getAmbiente());
		$soap = new CurlSoap();
		$soap->setCertificate($config->getArquivoChavePublica());
		$soap->setPrivateKey($config->getArquivoChavePrivada());
		$doh = $this->getNodeHeader($nota, $dom);
		$dob = $this->getNodeBody($nota, $dom);
		$resp = $soap->send($info['autorizacao'], $dob, $doh);
		$this->carrega($resp);
		if($this->getStatus() == '104') {
			$protocolo = new Protocolo();
			$protocolo->carrega($resp);
			$nota->setProtocolo($protocolo);
		} else {
			$nota->setProtocolo(null);
		}
	}

	public function carrega(&$dom) {
		$retorno = $dom->getElementsByTagName('retEnviNFe')->item(0);
		$this->setAmbiente($retorno->getElementsByTagName('tpAmb')->item(0)->nodeValue);
		$this->setVersao($retorno->getElementsByTagName('verAplic')->item(0)->nodeValue);
		$this->setStatus($retorno->getElementsByTagName('cStat')->item(0)->nodeValue);
		$this->setMotivo($retorno->getElementsByTagName('xMotivo')->item(0)->nodeValue);
		$this->setUF($retorno->getElementsByTagName('cUF')->item(0)->nodeValue);
		$data_recebimento = $retorno->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
		$data_recebimento = strtotime($data_recebimento);
		$this->setDataRecebimento($data_recebimento);
	}

	public function getNode($name = null) {}

}