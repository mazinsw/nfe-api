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
 * Classe para validação da nota fiscal eletrônica do consumidor
 */
class NFCe extends NF {

	const QRCODE_VERSAO = '100';

	public function __construct($nfce = array()) {
		parent::__construct($nfce);
		$this->setModelo(65);
		$this->setFormato(self::FORMATO_CONSUMIDOR);
	}

	public function toArray() {
		$nfce = parent::toArray();
		return $nfce;
	}

	public function fromArray($nfce = array()) {
		if($nfce instanceof NFCe)
			$nfce = $nfce->toArray();
		else if(!is_array($nfce))
			return $this;
		parent::fromArray($nfce);
		return $this;
	}

	private function gerarQRCodeInfo(&$dom) {
		$config = SEFAZ::getInstance()->getConfiguracao();
		$totais = $this->getTotais();
		$digest = $dom->getElementsByTagName('DigestValue')->item(0);
		if($this->getEmissao() == self::EMISSAO_NORMAL)
			$dig_val = $digest->nodeValue;
		else
			$dig_val = base64_encode(sha1($dom->saveXML(), true));
		$params = array(
			'chNFe' => $this->getID(),
			'nVersao' => self::QRCODE_VERSAO,
			'tpAmb' => $this->getAmbiente(true),
			'cDest' => null,
			'dhEmi' => Util::toHex($this->getDataEmissao(true)),
			'vNF' => Util::toCurrency($totais['nota']),
			'vICMS' => Util::toCurrency($totais[Imposto::GRUPO_ICMS]),
			'digVal' => Util::toHex($dig_val),
			'cIdToken' => Util::padDigit($config->getToken(), 6),
			'cHashQRCode' => null
		);
		if(!is_null($this->getCliente()->getID()))
			$params['cDest'] = $this->getCliente()->getID(true);
		else
			unset($params['cDest']);
		$_params = $params;
		unset($_params['cHashQRCode']);
		$query = http_build_query($_params);
		$params['cHashQRCode'] = sha1($query.$config->getCSC());
		return $params;
	}

	private function checkQRCode(&$dom) {
		$estado = $this->getEmitente()->getEndereco()->getMunicipio()->getEstado();
		$db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
		$params = $this->gerarQRCodeInfo($dom);
		$query = http_build_query($params);
		$info = $db->getInformacaoServico($this->getEmissao(), $estado->getUF(), 'nfce', $this->getAmbiente());
		$url = $info['qrcode'];
		$url .= (strpos($url, '?') === false?'?':'&').$query;
		$this->setConsultaURL($url);
		$this->setQrcodeData($url);
	}

	private function getNodeSuplementar(&$dom) {
		$this->checkQRCode($dom);
		$element = $dom->createElement(is_null($name)?'infNFeSupl':$name);
		$qrcode = $dom->createElement('qrCode');
		$data = $dom->createCDATASection($this->getConsultaURL(true));
		$qrcode->appendChild($data);
		$element->appendChild($qrcode);
		return $element;
	}

	/**
	 * Assina e adiciona informações suplementares da nota
	 */
	public function assinar($dom = null) {
		$dom = parent::assinar($dom);
		$suplementar = $this->getNodeSuplementar($dom);
		$signature = $dom->getElementsByTagName('Signature')->item(0);
		$signature->parentNode->insertBefore($suplementar, $signature);
		return $dom;
	}

}