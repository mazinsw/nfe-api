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
use Exception;
use DOMDocument;
use ValidationException;

class Situacao extends Retorno {

	private $chave;
	private $modelo;

	public function __construct($situacao = array()) {
		parent::__construct($situacao);
	}

	/**
	 * Chaves de acesso da NF-e, compostas por: UF do emitente, AAMM da emissão
	 * da NFe, CNPJ do emitente, modelo, série e número da NF-e e código
	 * numérico+DV.
	 */
	public function getChave($normalize = false) {
		if(!$normalize)
			return $this->chave;
		return $this->chave;
	}

	public function setChave($chave) {
		$this->chave = $chave;
		return $this;
	}

	public function getModelo($normalize = false) {
		if(!$normalize)
			return $this->modelo;
		return $this->modelo;
	}

	public function setModelo($modelo) {
		$this->modelo = $modelo;
		return $this;
	}

	public function toArray() {
		$situacao = parent::toArray();
		$situacao['chave'] = $this->getChave();
		$situacao['modelo'] = $this->getModelo();
		return $situacao;
	}

	public function fromArray($situacao = array()) {
		if($situacao instanceof Situacao)
			$situacao = $situacao->toArray();
		else if(!is_array($situacao))
			return $this;
		parent::fromArray($situacao);
		$this->setChave($situacao['chave']);
		$this->setModelo($situacao['modelo']);
		return $this;
	}

	public function envia($dom) {
		$envio = new Envio();
		$envio->setServico(Envio::SERVICO_PROTOCOLO);
		$envio->setAmbiente($this->getAmbiente());
		$envio->setModelo($this->getModelo());
		$envio->setEmissao(NF::EMISSAO_NORMAL);
		$envio->setConteudo($dom);
		$resp = $envio->envia();
		$this->loadNode($resp);
		if($this->isAutorizado()) {
			$protocolo = new Protocolo();
			$protocolo->loadNode($resp);
			return $protocolo;
		}
		return $this;
	}

	public function consulta($nota = null) {
		if(!is_null($nota)) {
			$this->setChave($nota->getID());
			$this->setAmbiente($nota->getAmbiente());
			$this->setModelo($nota->getModelo());
		}
		$dom = $this->getNode()->ownerDocument;
		$dom = $this->validar($dom);
		$retorno = $this->envia($dom);
		if($retorno instanceof Protocolo && $retorno->isAutorizado() && !is_null($nota))
			$nota->setProtocolo($retorno);
		return $retorno;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'consSitNFe':$name);
		$element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL);
		$versao = $dom->createAttribute('versao');
		$versao->value = NF::VERSAO;
		$element->appendChild($versao);

		$element->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
		$element->appendChild($dom->createElement('xServ', 'CONSULTAR'));
		$element->appendChild($dom->createElement('chNFe', $this->getChave(true)));
		$dom->appendChild($element);
		return $element;
	}

	public function loadNode($element, $name = null) {
		$name = is_null($name)?'retConsSitNFe':$name;
		$element = parent::loadNode($element, $name);
		$chave = null;
		$_fields = $element->getElementsByTagName('chNFe');
		if($_fields->length > 0)
			$chave = $_fields->item(0)->nodeValue;
		$this->setChave($chave);
		return $element;
	}

	/**
	 * Valida o documento após assinar
	 */
	public function validar($dom) {
		$dom->loadXML($dom->saveXML());
		$xsd_path = dirname(dirname(dirname(__FILE__))) . '/schema';
		$xsd_file = $xsd_path . '/consSitNFe_v3.10.xsd';
		if(!file_exists($xsd_file))
			throw new Exception('O arquivo "'.$xsd_file.'" de esquema XSD não existe!', 404);
		// Enable user error handling
		$save = libxml_use_internal_errors(true);
		if ($dom->schemaValidate($xsd_file)) {
			libxml_use_internal_errors($save);
			return $dom;
		}
		$msg = array();
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			$msg[] = 'Não foi possível validar o XML: '.$error->message;
		}
		libxml_clear_errors();
		libxml_use_internal_errors($save);
		throw new ValidationException($msg);
	}

}