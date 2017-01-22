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
use Util;
use SEFAZ;
use Estado;
use Exception;
use DOMDocument;
use FR3D\XmlDSig\Adapter\AdapterInterface;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;
use ValidationException;

class Evento extends Retorno {

	const VERSAO = '1.00';

	const TIPO_CANCELAMENTO = '110111';

	private $id;
	private $orgao;
	private $identificador;
	private $chave;
	private $data;
	private $tipo;
	private $sequencia;
	private $descricao;
	private $numero;
	private $justificativa;
	private $email;
	private $modelo;
	private $informacao;

	public function __construct($evento = array()) {
		parent::__construct($evento);
	}

	/**
	 * Identificador da TAG a ser assinada, a regra de formação do Id é: "ID" +
	 * tpEvento +  chave da NF-e + nSeqEvento
	 */
	public function getID($normalize = false) {
		if(!$normalize)
			return $this->id;
		return 'ID'.$this->id;
	}

	public function setID($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Código do órgão de recepção do Evento. Utilizar a Tabela do IBGE
	 * extendida, utilizar 91 para identificar o Ambiente Nacional
	 */
	public function getOrgao($normalize = false) {
		if(!$normalize || is_numeric($this->orgao))
			return $this->orgao;

		$db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
		return $db->getCodigoOrgao($this->orgao);
	}

	public function setOrgao($orgao) {
		$this->orgao = $orgao;
		return $this;
	}

	/**
	 * Identificação do  autor do evento
	 */
	public function getIdentificador($normalize = false) {
		if(!$normalize)
			return $this->identificador;
		return $this->identificador;
	}

	public function setIdentificador($identificador) {
		$this->identificador = $identificador;
		return $this;
	}

	/**
	 * Chave de Acesso da NF-e vinculada ao evento
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

	/**
	 * Data e Hora do Evento, formato UTC (AAAA-MM-DDThh:mm:ssTZD, onde TZD =
	 * +hh:mm ou -hh:mm)
	 */
	public function getData($normalize = false) {
		if(!$normalize)
			return $this->data;
		return Util::toDateTime($this->data);
	}

	public function setData($data) {
		if(!is_numeric($data))
			$data = strtotime($data);
		$this->data = $data;
		return $this;
	}

	/**
	 * Tipo do Evento
	 */
	public function getTipo($normalize = false) {
		if(!$normalize)
			return $this->tipo;
		return $this->tipo;
	}

	public function setTipo($tipo) {
		$this->tipo = $tipo;
		return $this;
	}

	/**
	 * Seqüencial do evento para o mesmo tipo de evento.  Para maioria dos
	 * eventos será 1, nos casos em que possa existir mais de um evento, como é
	 * o caso da carta de correção, o autor do evento deve numerar de forma
	 * seqüencial.
	 */
	public function getSequencia($normalize = false) {
		if(!$normalize)
			return $this->sequencia;
		return $this->sequencia;
	}

	public function setSequencia($sequencia) {
		$this->sequencia = $sequencia;
		return $this;
	}

	/**
	 * Descrição do Evento
	 */
	public function getDescricao($normalize = false) {
		if(!$normalize)
			return $this->descricao;
		return $this->descricao;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
		return $this;
	}

	/**
	 * Número do Protocolo de Status da NF-e. 1 posição (1 – Secretaria de
	 * Fazenda Estadual 2 – Receita Federal); 2 posições ano; 10 seqüencial no
	 * ano.
	 */
	public function getNumero($normalize = false) {
		if(!$normalize)
			return $this->numero;
		return $this->numero;
	}

	public function setNumero($numero) {
		$this->numero = $numero;
		return $this;
	}

	/**
	 * Justificativa do cancelamento
	 */
	public function getJustificativa($normalize = false) {
		if(!$normalize)
			return $this->justificativa;
		return $this->justificativa;
	}

	public function setJustificativa($justificativa) {
		$this->justificativa = $justificativa;
		return $this;
	}

	/**
	 * email do destinatário
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

	public function getModelo($normalize = false) {
		if(!$normalize)
			return $this->modelo;
		return $this->modelo;
	}

	public function setModelo($modelo) {
		$this->modelo = $modelo;
		return $this;
	}

	/**
	 * Resposta de informação do evento
	 */
	public function getInformacao() {
		return $this->informacao;
	}

	public function setInformacao($informacao) {
		$this->informacao = $informacao;
		return $this;
	}

	/**
	 * Informa se a identificação é um CNPJ
	 */
	public function isCNPJ() {
		return strlen($this->getIdentificador()) == 14;
	}

	/**
	 * Informa se o lote já foi processado e já tem um protocolo
	 */
	public function isProcessado() {
		return $this->getStatus() == '128';
	}

	/**
	 * Informa se a nota foi cancelada com sucesso
	 */
	public function isCancelado() {
		return in_array($this->getStatus(), array('135', '155'));
	}

	public function toArray() {
		$evento = parent::toArray();
		$evento['id'] = $this->getID();
		$evento['orgao'] = $this->getOrgao();
		$evento['identificador'] = $this->getIdentificador();
		$evento['chave'] = $this->getChave();
		$evento['data'] = $this->getData();
		$evento['tipo'] = $this->getTipo();
		$evento['sequencia'] = $this->getSequencia();
		$evento['descricao'] = $this->getDescricao();
		$evento['numero'] = $this->getNumero();
		$evento['justificativa'] = $this->getJustificativa();
		$evento['email'] = $this->getEmail();
		$evento['modelo'] = $this->getModelo();
		$evento['informacao'] = $this->getInformacao();
		return $evento;
	}

	public function fromArray($evento = array()) {
		if($evento instanceof Evento)
			$evento = $evento->toArray();
		else if(!is_array($evento))
			return $this;
		parent::fromArray($evento);
		$this->setID($evento['id']);
		$this->setOrgao($evento['orgao']);
		$this->setIdentificador($evento['identificador']);
		$this->setChave($evento['chave']);
		$this->setData($evento['data']);
		$this->setTipo($evento['tipo']);
		if(is_null($this->getTipo()))
			$this->setTipo(self::TIPO_CANCELAMENTO);
		$this->setSequencia($evento['sequencia']);
		if(is_null($this->getSequencia()))
			$this->setSequencia(1);
		$this->setDescricao($evento['descricao']);
		if(is_null($this->getDescricao()))
			$this->setDescricao('Cancelamento');
		$this->setNumero($evento['numero']);
		$this->setJustificativa($evento['justificativa']);
		$this->setEmail($evento['email']);
		$this->setModelo($evento['modelo']);
		$this->setInformacao($evento['informacao']);
		return $this;
	}

	/**
	 * Gera o ID, a regra de formação do Id é: "ID" +
	 * tpEvento +  chave da NF-e + nSeqEvento
	 */
	public function gerarID() {
		$id = sprintf('%s%s%02d',
			$this->getTipo(true),
			$this->getChave(true),
			$this->getSequencia(true)
		);
		return $id;
	}

	public function getNode($name = null) {
		$this->setID($this->gerarID());

		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'evento':$name);
		$element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL);
		$versao = $dom->createAttribute('versao');
		$versao->value = self::VERSAO;
		$element->appendChild($versao);

		$info = $dom->createElement('infEvento');
		$dom = $element->ownerDocument;
		$id = $dom->createAttribute('Id');
		$id->value = $this->getID(true);
		$info->appendChild($id);
		
		$info->appendChild($dom->createElement('cOrgao', $this->getOrgao(true)));
		$info->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
		if($this->isCNPJ())
			$info->appendChild($dom->createElement('CNPJ', $this->getIdentificador(true)));
		else
			$info->appendChild($dom->createElement('CPF', $this->getIdentificador(true)));
		$info->appendChild($dom->createElement('chNFe', $this->getChave(true)));
		$info->appendChild($dom->createElement('dhEvento', $this->getData(true)));
		$info->appendChild($dom->createElement('tpEvento', $this->getTipo(true)));
		$info->appendChild($dom->createElement('nSeqEvento', $this->getSequencia(true)));
		$info->appendChild($dom->createElement('verEvento', self::VERSAO));

		$detalhes = $dom->createElement('detEvento');
		$versao = $dom->createAttribute('versao');
		$versao->value = self::VERSAO;
		$detalhes->appendChild($versao);

		$detalhes->appendChild($dom->createElement('descEvento', $this->getDescricao(true)));
		$detalhes->appendChild($dom->createElement('nProt', $this->getNumero(true)));
		$detalhes->appendChild($dom->createElement('xJust', $this->getJustificativa(true)));
		$info->appendChild($detalhes);

		$element->appendChild($info);
		$dom->appendChild($element);
		return $element;
	}

	public function getReturnNode() {
		$outros = parent::getNode('infEvento');
		$element = $this->getNode('retEvento');
		$dom = $element->ownerDocument;
		$element->removeAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns');
		$info = $dom->getElementsByTagName('infEvento')->item(0);
		$info->removeAttribute('Id');
		$removeTags = array('detEvento', 'verEvento', 'dhEvento', 'CNPJ', 'CPF', 'cOrgao');
		foreach ($removeTags as $key) {
			$_fields = $info->getElementsByTagName($key);
			if($_fields->length == 0)
				continue;
			$node = $_fields->item(0);
			$info->removeChild($node);
		}
		$chave = $info->getElementsByTagName('chNFe')->item(0);
		foreach ($outros->childNodes as $node) {
			$node = $dom->importNode($node, true);
			$list = $info->getElementsByTagName($node->nodeName);
			if($list->length == 1)
				continue;
			$info->insertBefore($node, $chave);
		}
		$status = $info->getElementsByTagName('cStat')->item(0);
		$info->insertBefore($dom->createElement('cOrgao', $this->getOrgao(true)), $status);
		$sequencia = $info->getElementsByTagName('nSeqEvento')->item(0);
		$info->insertBefore($dom->createElement('xEvento', $this->getDescricao(true)), $sequencia);
		if(!is_null($this->getIdentificador())) {
			if($this->isCNPJ())
				$info->appendChild($dom->createElement('CNPJDest', $this->getIdentificador(true)));
			else
				$info->appendChild($dom->createElement('CPFDest', $this->getIdentificador(true)));
		}
		if(!is_null($this->getEmail()))
			$info->appendChild($dom->createElement('emailDest', $this->getEmail(true)));
		$info->appendChild($dom->createElement('dhRegEvento', $this->getData(true)));
		$info->appendChild($dom->createElement('nProt', $this->getNumero(true)));
		return $element;
	}

	public function loadNode($element, $name = null) {
		$name = is_null($name)?'infEvento':$name;
		$element = parent::loadNode($element, $name);
		$_fields = $element->getElementsByTagName('cOrgao');
		if($_fields->length > 0)
			$orgao = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "cOrgao" do campo "Orgao" não encontrada', 404);
		$this->setOrgao($orgao);
		if($name == 'retEnvEvento')
			return $element;
		$_fields = $element->getElementsByTagName('chNFe');
		if($_fields->length > 0)
			$chave = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "chNFe" do campo "Chave" não encontrada', 404);
		$this->setChave($chave);
		$_fields = $element->getElementsByTagName('tpEvento');
		if($_fields->length > 0)
			$tipo = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "tpEvento" do campo "Tipo" não encontrada', 404);
		$this->setTipo($tipo);
		$_fields = $element->getElementsByTagName('xEvento');
		if($_fields->length > 0)
			$descricao = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "xEvento" do campo "Descricao" não encontrada', 404);
		$this->setDescricao($descricao);
		$_fields = $element->getElementsByTagName('nSeqEvento');
		if($_fields->length > 0)
			$sequencia = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "nSeqEvento" do campo "Sequencia" não encontrada', 404);
		$this->setSequencia($sequencia);
		$_fields = $element->getElementsByTagName('dhRegEvento');
		if($_fields->length > 0)
			$data = $_fields->item(0)->nodeValue;
		else
			throw new Exception('Tag "dhRegEvento" do campo "Data" não encontrada', 404);
		$this->setData($data);
		$identificador = null;
		$_fields = $element->getElementsByTagName('CNPJDest');
		if($_fields->length == 0)
			$_fields = $element->getElementsByTagName('CPFDest');
		if($_fields->length > 0)
			$identificador = $_fields->item(0)->nodeValue;
		$this->setIdentificador($identificador);
		$email = null;
		$_fields = $element->getElementsByTagName('emailDest');
		if($_fields->length > 0)
			$email = $_fields->item(0)->nodeValue;
		$this->setEmail($email);
		$numero = null;
		$_fields = $element->getElementsByTagName('nProt');
		if($_fields->length > 0)
			$numero = $_fields->item(0)->nodeValue;
		$this->setNumero($numero);
		return $element;
	}

	private function getConteudo($dom) {
		$config = SEFAZ::getInstance()->getConfiguracao();
		$dob = new DOMDocument('1.0', 'UTF-8');
		$envio = $dob->createElement('envEvento');
		$envio->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL);
		$versao = $dob->createAttribute('versao');
		$versao->value = self::VERSAO;
		$envio->appendChild($versao);
		$envio->appendChild($dob->createElement('idLote', self::genLote()));
		// Corrige xmlns:default
		// $data = $dob->importNode($dom->documentElement, true);
		// $envio->appendChild($data);
		$envio->appendChild($dob->createElement('evento', 0)); 
		$dob->appendChild($envio);
		// Corrige xmlns:default
		// return $dob;
		$xml = $dob->saveXML($dob->documentElement);
		return str_replace('<evento>0</evento>', $dom->saveXML($dom->documentElement), $xml);
	}

	public function envia($dom) {
		$envio = new Envio();
		$envio->setServico(Envio::SERVICO_EVENTO);
		$envio->setAmbiente($this->getAmbiente());
		$envio->setModelo($this->getModelo());
		$envio->setEmissao(NF::EMISSAO_NORMAL);
		$envio->setConteudo($this->getConteudo($dom));
		$resp = $envio->envia();
		$this->loadNode($resp, 'retEnvEvento');
		if(!$this->isProcessado())
			throw new Exception($this->getMotivo(), $this->getStatus());
		$retorno = new Evento();
		$retorno->loadNode($resp);
		$this->setInformacao($retorno);
		return $retorno;
	}

	/**
	 * Adiciona a informação no XML do evento
	 */
	public function addInformacao($dom) {
		if(is_null($this->getInformacao()))
			throw new Exception('A informação não foi informado no evento "'.$this->getID().'"', 404);
		$evento = $dom->getElementsByTagName('evento')->item(0);
		// Corrige xmlns:default
		$evento_xml = $dom->saveXML($evento);

		$element = $dom->createElement('procEventoNFe');
		$element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL);
		$versao = $dom->createAttribute('versao');
		$versao->value = self::VERSAO;
		$element->appendChild($versao);
		$dom->removeChild($evento);
		// Corrige xmlns:default
		$evento = $dom->createElement('evento', 0);

		$element->appendChild($evento);
		$info = $this->getInformacao()->getReturnNode();
		$info = $dom->importNode($info, true);
		$element->appendChild($info);
		$dom->appendChild($element);
		// Corrige xmlns:default
		$xml = $dom->saveXML();
		$xml = str_replace('<evento>0</evento>', $evento_xml, $xml);
		$dom->loadXML($xml);

		return $dom;
	}

	/**
	 * Assina o XML com a assinatura eletrônica do tipo A1
	 */
	public function assinar($dom = null) {
		if(is_null($dom)) {
			$xml = $this->getNode();
			$dom = $xml->ownerDocument;
		}
		$config = SEFAZ::getInstance()->getConfiguracao();

		$adapter = new XmlseclibsAdapter();
		$adapter->setPrivateKey($config->getChavePrivada());
		$adapter->setPublicKey($config->getChavePublica());
		$adapter->addTransform(AdapterInterface::ENVELOPED);
		$adapter->addTransform(AdapterInterface::XML_C14N);
		$adapter->sign($dom, 'infEvento');
		return $dom;
	}

	/**
	 * Valida o documento após assinar
	 */
	public function validar($dom) {
		$dom->loadXML($dom->saveXML());
		$xsd_path = dirname(dirname(dirname(__FILE__))) . '/schema';
		$xsd_file = $xsd_path . '/cancelamento/eventoCancNFe_v1.00.xsd';
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