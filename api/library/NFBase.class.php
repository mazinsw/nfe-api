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
use FR3D\XmlDSig\Adapter\AdapterInterface;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;

class NFBaseTipo {
	const ENTRADA = 'entrada';
	const SAIDA = 'saida';
}

class NFBaseDestino {
	const INTERNA = 'interna';
	const INTERESTADUAL = 'interestadual';
	const EXTERIOR = 'exterior';
}

class NFBaseIndicador {
	const AVISTA = 'avista';
	const APRAZO = 'aprazo';
	const OUTROS = 'outros';
}

class NFBaseFormatoImpressao {
	const NENHUMA = 'nenhuma';
	const RETRATO = 'retrato';
	const PAISAGEM = 'paisagem';
	const SIMPLIFICADO = 'simplificado';
	const CONSUMIDOR = 'consumidor';
	const MENSAGEM = 'mensagem';
}

class NFBaseFormaEmissao {
	const NORMAL = 'normal';
	const CONTINGENCIA = 'contingencia';
}

class NFBaseAmbiente {
	const PRODUCAO = 'producao';
	const HOMOLOGACAO = 'homologacao';
}

class NFBaseFinalidade {
	const NORMAL = 'normal';
	const COMPLEMENTAR = 'complementar';
	const AJUSTE = 'ajuste';
	const RETORNO = 'retorno';
}

class NFBasePresencaComprador {
	const NENHUM = 'nenhum';
	const PRESENCIAL = 'presencial';
	const INTERNET = 'internet';
	const TELEATENDIMENTO = 'teleatendimento';
	const ENTREGA = 'entrega';
	const OUTROS = 'outros';
}

/**
 * Classe base para a formação da nota fiscal
 */
abstract class NFBase implements NodeInterface {

	private $id;
	private $numero;
	private $emitente;
	private $cliente;
	private $produtos;
	private $pagamentos;
	private $consulta_url;
	private $qrcode_data;
	private $data_saida;
	private $data_entrada;
	private $modelo;
	private $tipo;
	private $destino;
	private $natureza;
	private $codigo;
	private $indicador;
	private $data_emissao;
	private $serie;
	private $formato_impressao;
	private $forma_emissao;
	private $digito_verificador;
	private $ambiente;
	private $finalidade;
	private $consumidor_final;
	private $presenca_comprador;

	const VERSAO = '1.0';

	public function __construct($nfbase = array()) {
		$this->fromArray($nfbase);
	}

	public function getID($normalize = false) {
		if(!$normalize)
			return $this->id;
		return 'NFe'.$this->id;
	}

	public function setID($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Número do Documento Fiscal
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

	public function getEmitente() {
		return $this->emitente;
	}

	public function setEmitente($emitente) {
		$this->emitente = $emitente;
		return $this;
	}

	public function getCliente() {
		return $this->cliente;
	}

	public function setCliente($cliente) {
		$this->cliente = $cliente;
		return $this;
	}

	public function getProdutos() {
		return $this->produtos;
	}

	public function setProdutos($produtos) {
		$this->produtos = $produtos;
		return $this;
	}

	public function addProduto($produto) {
		$this->produtos[] = $produto;
		return $this;
	}

	public function getPagamentos() {
		return $this->pagamentos;
	}

	public function setPagamentos($pagamentos) {
		$this->pagamentos = $pagamentos;
		return $this;
	}

	public function addPagamento($pagamento) {
		$this->pagamentos[] = $pagamento;
		return $this;
	}

	public function getConsultaURL($normalize = false) {
		if(!$normalize)
			return $this->consulta_url;
		return $this->consulta_url;
	}

	public function setConsultaURL($consulta_url) {
		$this->consulta_url = $consulta_url;
		return $this;
	}

	public function getQrcodeData($normalize = false) {
		if(!$normalize)
			return $this->qrcode_data;
		return $this->qrcode_data;
	}

	public function setQrcodeData($qrcode_data) {
		$this->qrcode_data = $qrcode_data;
		return $this;
	}

	/**
	 * Data e Hora da saída da mercadoria / produto
	 */
	public function getDataSaida($normalize = false) {
		if(!$normalize)
			return $this->data_saida;
		return Util::toDateTime($this->data_saida);
	}

	public function setDataSaida($data_saida) {
		$this->data_saida = $data_saida;
		return $this;
	}

	/**
	 * Data e Hora de entrada da mercadoria / produto
	 */
	public function getDataEntrada($normalize = false) {
		if(!$normalize)
			return $this->data_entrada;
		return Util::toDateTime($this->data_entrada);
	}

	public function setDataEntrada($data_entrada) {
		$this->data_entrada = $data_entrada;
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
	 * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
	 */
	public function getTipo($normalize = false) {
		if(!$normalize)
			return $this->tipo;
		switch ($this->tipo) {
			case NFBaseTipo::ENTRADA:
				return '0';
			case NFBaseTipo::SAIDA:
				return '1';
		}
		return $this->tipo;
	}

	public function setTipo($tipo) {
		$this->tipo = $tipo;
		return $this;
	}

	/**
	 * Identificador de Local de destino da operação
	 * (1-Interna;2-Interestadual;3-Exterior)
	 */
	public function getDestino($normalize = false) {
		if(!$normalize)
			return $this->destino;
		switch ($this->destino) {
			case NFBaseDestino::INTERNA:
				return '1';
			case NFBaseDestino::INTERESTADUAL:
				return '2';
			case NFBaseDestino::EXTERIOR:
				return '3';
		}
		return $this->destino;
	}

	public function setDestino($destino) {
		$this->destino = $destino;
		return $this;
	}

	/**
	 * Descrição da Natureza da Operação
	 */
	public function getNatureza($normalize = false) {
		if(!$normalize)
			return $this->natureza;
		return $this->natureza;
	}

	public function setNatureza($natureza) {
		$this->natureza = $natureza;
		return $this;
	}

	/**
	 * Código numérico que compõe a Chave de Acesso. Número aleatório gerado
	 * pelo emitente para cada NF-e.
	 */
	public function getCodigo($normalize = false) {
		if(!$normalize)
			return $this->codigo;
		return $this->codigo;
	}

	public function setCodigo($codigo) {
		$this->codigo = $codigo;
		return $this;
	}

	/**
	 * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
	 * prazo; 2 – outros.
	 */
	public function getIndicador($normalize = false) {
		if(!$normalize)
			return $this->indicador;
		switch ($this->indicador) {
			case NFBaseIndicador::AVISTA:
				return '0';
			case NFBaseIndicador::APRAZO:
				return '1';
			case NFBaseIndicador::OUTROS:
				return '2';
		}
		return $this->indicador;
	}

	public function setIndicador($indicador) {
		$this->indicador = $indicador;
		return $this;
	}

	/**
	 * Data e Hora de emissão do Documento Fiscal
	 */
	public function getDataEmissao($normalize = false) {
		if(!$normalize)
			return $this->data_emissao;
		return Util::toDateTime($this->data_emissao);
	}

	public function setDataEmissao($data_emissao) {
		$this->data_emissao = $data_emissao;
		return $this;
	}

	/**
	 * Série do Documento Fiscal: série normal 0-889, Avulsa Fisco 890-899,
	 * SCAN 900-999
	 */
	public function getSerie($normalize = false) {
		if(!$normalize)
			return $this->serie;
		return $this->serie;
	}

	public function setSerie($serie) {
		$this->serie = $serie;
		return $this;
	}

	/**
	 * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
	 * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
	 * eletrônica)
	 */
	public function getFormatoImpressao($normalize = false) {
		if(!$normalize)
			return $this->formato_impressao;
		switch ($this->formato_impressao) {
			case NFBaseFormatoImpressao::NENHUMA:
				return '0';
			case NFBaseFormatoImpressao::RETRATO:
				return '1';
			case NFBaseFormatoImpressao::PAISAGEM:
				return '2';
			case NFBaseFormatoImpressao::SIMPLIFICADO:
				return '3';
			case NFBaseFormatoImpressao::CONSUMIDOR:
				return '4';
			case NFBaseFormatoImpressao::MENSAGEM:
				return '5';
		}
		return $this->formato_impressao;
	}

	public function setFormatoImpressao($formato_impressao) {
		$this->formato_impressao = $formato_impressao;
		return $this;
	}

	/**
	 * Forma de emissão da NF-e
	 */
	public function getFormaEmissao($normalize = false) {
		if(!$normalize)
			return $this->forma_emissao;
		switch ($this->forma_emissao) {
			case NFBaseFormaEmissao::NORMAL:
				return '1';
			case NFBaseFormaEmissao::CONTINGENCIA:
				return '9';
		}
		return $this->forma_emissao;
	}

	public function setFormaEmissao($forma_emissao) {
		$this->forma_emissao = $forma_emissao;
		return $this;
	}

	/**
	 * Digito Verificador da Chave de Acesso da NF-e
	 */
	public function getDigitoVerificador($normalize = false) {
		if(!$normalize)
			return $this->digito_verificador;
		return $this->digito_verificador;
	}

	public function setDigitoVerificador($digito_verificador) {
		$this->digito_verificador = $digito_verificador;
		return $this;
	}

	/**
	 * Identificação do Ambiente: 1 - Produção, 2 - Homologação
	 */
	public function getAmbiente($normalize = false) {
		if(!$normalize)
			return $this->ambiente;
		switch ($this->ambiente) {
			case NFBaseAmbiente::PRODUCAO:
				return '1';
			case NFBaseAmbiente::HOMOLOGACAO:
				return '2';
		}
		return $this->ambiente;
	}

	public function setAmbiente($ambiente) {
		$this->ambiente = $ambiente;
		return $this;
	}

	/**
	 * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
	 * NFe de ajuste, 4 - Devolução/Retorno
	 */
	public function getFinalidade($normalize = false) {
		if(!$normalize)
			return $this->finalidade;
		switch ($this->finalidade) {
			case NFBaseFinalidade::NORMAL:
				return '1';
			case NFBaseFinalidade::COMPLEMENTAR:
				return '2';
			case NFBaseFinalidade::AJUSTE:
				return '3';
			case NFBaseFinalidade::RETORNO:
				return '4';
		}
		return $this->finalidade;
	}

	public function setFinalidade($finalidade) {
		$this->finalidade = $finalidade;
		return $this;
	}

	/**
	 * Indica operação com consumidor final (0-Não;1-Consumidor Final)
	 */
	public function getConsumidorFinal($normalize = false) {
		if(!$normalize)
			return $this->consumidor_final;
		switch ($this->consumidor_final) {
			case 'N':
				return '0';
			case 'Y':
				return '1';
		}
		return $this->consumidor_final;
	}

	/**
	 * Indica operação com consumidor final (0-Não;1-Consumidor Final)
	 */
	public function isConsumidorFinal() {
		return $this->consumidor_final == 'Y';
	}

	public function setConsumidorFinal($consumidor_final) {
		$this->consumidor_final = $consumidor_final;
		return $this;
	}

	/**
	 * Indicador de presença do comprador no estabelecimento comercial no
	 * momento da oepração (0-Não se aplica, ex.: Nota Fiscal complementar ou
	 * de ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
	 * presencial, teleatendimento;4-NFC-e entrega em domicílio;9-Não
	 * presencial, outros)
	 */
	public function getPresencaComprador($normalize = false) {
		if(!$normalize)
			return $this->presenca_comprador;
		switch ($this->presenca_comprador) {
			case NFBasePresencaComprador::NENHUM:
				return '0';
			case NFBasePresencaComprador::PRESENCIAL:
				return '1';
			case NFBasePresencaComprador::INTERNET:
				return '2';
			case NFBasePresencaComprador::TELEATENDIMENTO:
				return '3';
			case NFBasePresencaComprador::ENTREGA:
				return '4';
			case NFBasePresencaComprador::OUTROS:
				return '9';
		}
		return $this->presenca_comprador;
	}

	public function setPresencaComprador($presenca_comprador) {
		$this->presenca_comprador = $presenca_comprador;
		return $this;
	}

	public function toArray() {
		$nfbase = array();
		$nfbase['id'] = $this->getID();
		$nfbase['numero'] = $this->getNumero();
		$nfbase['emitente'] = $this->getEmitente();
		$nfbase['cliente'] = $this->getCliente();
		$nfbase['produtos'] = $this->getProdutos();
		$nfbase['pagamentos'] = $this->getPagamentos();
		$nfbase['consulta_url'] = $this->getConsultaURL();
		$nfbase['qrcode_data'] = $this->getQrcodeData();
		$nfbase['data_saida'] = $this->getDataSaida();
		$nfbase['data_entrada'] = $this->getDataEntrada();
		$nfbase['modelo'] = $this->getModelo();
		$nfbase['tipo'] = $this->getTipo();
		$nfbase['destino'] = $this->getDestino();
		$nfbase['natureza'] = $this->getNatureza();
		$nfbase['codigo'] = $this->getCodigo();
		$nfbase['indicador'] = $this->getIndicador();
		$nfbase['data_emissao'] = $this->getDataEmissao();
		$nfbase['serie'] = $this->getSerie();
		$nfbase['formato_impressao'] = $this->getFormatoImpressao();
		$nfbase['forma_emissao'] = $this->getFormaEmissao();
		$nfbase['digito_verificador'] = $this->getDigitoVerificador();
		$nfbase['ambiente'] = $this->getAmbiente();
		$nfbase['finalidade'] = $this->getFinalidade();
		$nfbase['consumidor_final'] = $this->getConsumidorFinal();
		$nfbase['presenca_comprador'] = $this->getPresencaComprador();
		return $nfbase;
	}

	public function fromArray($nfbase = array()) {
		if($nfbase instanceof NFBase)
			$nfbase = $nfbase->toArray();
		else if(!is_array($nfbase))
			return $this;
		$this->setID($nfbase['id']);
		$this->setNumero($nfbase['numero']);
		$this->setEmitente($nfbase['emitente']);
		if(is_null($this->getEmitente()))
			$this->setEmitente(new Emitente());
		$this->setCliente($nfbase['cliente']);
		if(is_null($this->getCliente()))
			$this->setCliente(new Cliente());
		$this->setProdutos($nfbase['produtos']);
		if(is_null($this->getProdutos()))
			$this->setProdutos(array());
		$this->setPagamentos($nfbase['pagamentos']);
		if(is_null($this->getPagamentos()))
			$this->setPagamentos(array());
		$this->setConsultaURL($nfbase['consulta_url']);
		$this->setQrcodeData($nfbase['qrcode_data']);
		$this->setDataSaida($nfbase['data_saida']);
		$this->setDataEntrada($nfbase['data_entrada']);
		$this->setModelo($nfbase['modelo']);
		$this->setTipo($nfbase['tipo']);
		if(is_null($this->getTipo()))
			$this->setTipo(NFBaseTipo::SAIDA);
		$this->setDestino($nfbase['destino']);
		if(is_null($this->getDestino()))
			$this->setDestino(NFBaseDestino::INTERNA);
		$this->setNatureza($nfbase['natureza']);
		if(is_null($this->getNatureza()))
			$this->setNatureza('VENDA PARA CONSUMIDOR FINAL');
		$this->setCodigo($nfbase['codigo']);
		$this->setIndicador($nfbase['indicador']);
		if(is_null($this->getIndicador()))
			$this->setIndicador(NFBaseIndicador::AVISTA);
		$this->setDataEmissao($nfbase['data_emissao']);
		$this->setSerie($nfbase['serie']);
		$this->setFormatoImpressao($nfbase['formato_impressao']);
		if(is_null($this->getFormatoImpressao()))
			$this->setFormatoImpressao(NFBaseFormatoImpressao::CONSUMIDOR);
		$this->setFormaEmissao($nfbase['forma_emissao']);
		if(is_null($this->getFormaEmissao()))
			$this->setFormaEmissao(NFBaseFormaEmissao::NORMAL);
		$this->setDigitoVerificador($nfbase['digito_verificador']);
		$this->setAmbiente($nfbase['ambiente']);
		if(is_null($this->getAmbiente()))
			$this->setAmbiente(NFBaseAmbiente::HOMOLOGACAO);
		$this->setFinalidade($nfbase['finalidade']);
		if(is_null($this->getFinalidade()))
			$this->setFinalidade(NFBaseFinalidade::NORMAL);
		$this->setConsumidorFinal($nfbase['consumidor_final']);
		if(is_null($this->getConsumidorFinal()))
			$this->setConsumidorFinal('Y');
		$this->setPresencaComprador($nfbase['presenca_comprador']);
		return $this;
	}

	public function gerarID() {
        $id = sprintf('%02d%02d%02d%s%02d%03d%09d%01d%08d',
            41,  // TODO: get config[Código do estado]
            date('y', $this->getDataEmissao()), // Ano 2 dígitos
            date('m', $this->getDataEmissao()), // Mês 2 dígitos
            $this->getEmitente()->getCNPJ(),
            $this->getModelo(true),
            $this->getSerie(),
            $this->getNumero(),
            $this->getFormaEmissao(true),
            $this->getCodigo()
        );
        return $id.Util::getDAC($id, 11);

	}

	public function getNode($name = null) {
		$this->setID($this->gerarID());
		$this->setDigitoVerificador(substr($this->getID(), -1, 1));

		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'nfeProc':$name);
		$xmlns = $dom->createAttribute('xmlns');
		$xmlns->value = 'http://www.portalfiscal.inf.br/nfe';
		$element->appendChild($xmlns);
		$versao = $dom->createAttribute('versao');
		$versao->value = '3.10';
		$element->appendChild($versao);

		$nota = $dom->createElement('NFe');
		$xmlns = $dom->createAttribute('xmlns');
		$xmlns->value = 'http://www.portalfiscal.inf.br/nfe';
		$nota->appendChild($xmlns);

		$info = $dom->createElement('infNFe');
		$id = $dom->createAttribute('id');
		$id->value = $this->getID(true);
		$info->appendChild($id);
		$versao = $dom->createAttribute('versao');
		$versao->value = '3.10';
		$info->appendChild($versao);

		$ident = $dom->createElement('ide');
		$ident->appendChild($dom->createElement('cUF', 41)); // TODO: get config[Código do estado]
		$ident->appendChild($dom->createElement('cNF', $this->getCodigo(true)));
		$ident->appendChild($dom->createElement('natOp', $this->getNatureza(true)));
		$ident->appendChild($dom->createElement('indPag', $this->getIndicador(true)));
		$ident->appendChild($dom->createElement('mod', $this->getModelo(true)));
		$ident->appendChild($dom->createElement('serie', $this->getSerie(true)));
		$ident->appendChild($dom->createElement('nNF', $this->getNumero(true)));
		$ident->appendChild($dom->createElement('dhEmi', $this->getDataEmissao(true)));
		$ident->appendChild($dom->createElement('tpNF', $this->getTipo(true)));
		$ident->appendChild($dom->createElement('idDest', $this->getDestino(true)));
		$ident->appendChild($dom->createElement('cMunFG', $this->getEmitente()
															   ->getEndereco()
															   ->getMunicipio()
															   ->getCodigo(true)));
		$ident->appendChild($dom->createElement('tpImp', $this->getFormatoImpressao(true)));
		$ident->appendChild($dom->createElement('tpEmis', $this->getFormaEmissao(true)));
		$ident->appendChild($dom->createElement('cDV', $this->getDigitoVerificador(true)));
		$ident->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
		$ident->appendChild($dom->createElement('finNFe', $this->getFinalidade(true)));
		$ident->appendChild($dom->createElement('indFinal', $this->getConsumidorFinal(true)));
		$ident->appendChild($dom->createElement('indPres', $this->getPresencaComprador(true)));
		$ident->appendChild($dom->createElement('procEmi', 0));
		$ident->appendChild($dom->createElement('verProc', self::VERSAO));

		$info->appendChild($ident);

		$emitente = $this->getEmitente()->getNode();
		$emitente = $dom->importNode($emitente, true);
		$info->appendChild($emitente);
		$cliente = $this->getCliente()->getNode();
		$cliente = $dom->importNode($cliente, true);
		$info->appendChild($cliente);
		$_produtos = $this->getProdutos();
		foreach ($_produtos as $_produto) {
			$produto = $_produto->getNode();
			$produto = $dom->importNode($produto, true);
			$info->appendChild($produto);
		}
		// TODO: adicionar total
		// TODO: adicionar transportadora
		// TODO: adicionar cobrança
		$_pagamentos = $this->getPagamentos();
		foreach ($_pagamentos as $_pagamento) {
			$pagamento = $_pagamento->getNode();
			$pagamento = $dom->importNode($pagamento, true);
			$info->appendChild($pagamento);
		}
		// TODO: adicionar informações adicionais
		// TODO: adicionar exportação
		// TODO: adicionar compra
		// TODO: adicionar cana
		$nota->appendChild($info);
		$element->appendChild($nota);
		$dom->appendChild($element);
		return $element;
	}

	public static function assinar()
	{
        $data = new DOMDocument();
        $data->load(__DIR__ . '/../../tests/xml/nfe.xml');

        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey(file_get_contents(__DIR__ . '/../../tests/cert/private.pem'));
        $adapter->setPublicKey(file_get_contents(__DIR__ . '/../../tests/cert/public.pem'));
        $adapter->addTransform(AdapterInterface::ENVELOPED);
        $adapter->addTransform(AdapterInterface::XML_C14N);
        $adapter->sign($data, 'infNFe');
        file_put_contents(__DIR__ . '/../../tests/xml/nfe.test.signed.xml', $data->saveXML());

        // verificar
        $data = new DOMDocument();
        $data->load(__DIR__ . '/../../tests/xml/nfe.test.signed.xml');

        $adapter = new XmlseclibsAdapter();
        var_dump($adapter->verify($data));
	}

}
