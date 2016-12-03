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

/**
 * Classe base para a formação da nota fiscal
 */
abstract class NF implements NodeInterface {

	const VERSAO = '1.0';

	/**
	 * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
	 */
	const TIPO_ENTRADA = 'entrada';
	const TIPO_SAIDA = 'saida';

	/**
	 * Identificador de Local de destino da operação
	 * (1-Interna;2-Interestadual;3-Exterior)
	 */
	const DESTINO_INTERNA = 'interna';
	const DESTINO_INTERESTADUAL = 'interestadual';
	const DESTINO_EXTERIOR = 'exterior';

	/**
	 * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
	 * prazo; 2 – outros.
	 */
	const INDICADOR_AVISTA = 'avista';
	const INDICADOR_APRAZO = 'aprazo';
	const INDICADOR_OUTROS = 'outros';

	/**
	 * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
	 * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
	 * eletrônica)
	 */
	const FORMATO_NENHUMA = 'nenhuma';
	const FORMATO_RETRATO = 'retrato';
	const FORMATO_PAISAGEM = 'paisagem';
	const FORMATO_SIMPLIFICADO = 'simplificado';
	const FORMATO_CONSUMIDOR = 'consumidor';
	const FORMATO_MENSAGEM = 'mensagem';

	/**
	 * Forma de emissão da NF-e
	 */
	const EMISSAO_NORMAL = 'normal';
	const EMISSAO_CONTINGENCIA = 'contingencia';

	/**
	 * Identificação do Ambiente: 1 - Produção, 2 - Homologação
	 */
	const AMBIENTE_PRODUCAO = 'producao';
	const AMBIENTE_HOMOLOGACAO = 'homologacao';

	/**
	 * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
	 * NFe de ajuste, 4 - Devolução/Retorno
	 */
	const FINALIDADE_NORMAL = 'normal';
	const FINALIDADE_COMPLEMENTAR = 'complementar';
	const FINALIDADE_AJUSTE = 'ajuste';
	const FINALIDADE_RETORNO = 'retorno';

	/**
	 * Indicador de presença do comprador no estabelecimento comercial no
	 * momento da oepração (0-Não se aplica, ex.: Nota Fiscal complementar ou
	 * de ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
	 * presencial, teleatendimento;4-NFC-e entrega em domicílio;9-Não
	 * presencial, outros)
	 */
	const PRESENCA_NENHUM = 'nenhum';
	const PRESENCA_PRESENCIAL = 'presencial';
	const PRESENCA_INTERNET = 'internet';
	const PRESENCA_TELEATENDIMENTO = 'teleatendimento';
	const PRESENCA_ENTREGA = 'entrega';
	const PRESENCA_OUTROS = 'outros';

	private $id;
	private $numero;
	private $emitente;
	private $cliente;
	private $produtos;
	private $transporte;
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
	private $formato;
	private $emissao;
	private $digito_verificador;
	private $ambiente;
	private $finalidade;
	private $consumidor_final;
	private $presenca;

	public function __construct($nf = array()) {
		$this->fromArray($nf);
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

	public function getTransporte() {
		return $this->transporte;
	}

	public function setTransporte($transporte) {
		$this->transporte = $transporte;
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
			case self::TIPO_ENTRADA:
				return '0';
			case self::TIPO_SAIDA:
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
			case self::DESTINO_INTERNA:
				return '1';
			case self::DESTINO_INTERESTADUAL:
				return '2';
			case self::DESTINO_EXTERIOR:
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
			case self::INDICADOR_AVISTA:
				return '0';
			case self::INDICADOR_APRAZO:
				return '1';
			case self::INDICADOR_OUTROS:
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
	public function getFormato($normalize = false) {
		if(!$normalize)
			return $this->formato;
		switch ($this->formato) {
			case self::FORMATO_NENHUMA:
				return '0';
			case self::FORMATO_RETRATO:
				return '1';
			case self::FORMATO_PAISAGEM:
				return '2';
			case self::FORMATO_SIMPLIFICADO:
				return '3';
			case self::FORMATO_CONSUMIDOR:
				return '4';
			case self::FORMATO_MENSAGEM:
				return '5';
		}
		return $this->formato;
	}

	public function setFormato($formato) {
		$this->formato = $formato;
		return $this;
	}

	/**
	 * Forma de emissão da NF-e
	 */
	public function getEmissao($normalize = false) {
		if(!$normalize)
			return $this->emissao;
		switch ($this->emissao) {
			case self::EMISSAO_NORMAL:
				return '1';
			case self::EMISSAO_CONTINGENCIA:
				return '9';
		}
		return $this->emissao;
	}

	public function setEmissao($emissao) {
		$this->emissao = $emissao;
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
			case self::AMBIENTE_PRODUCAO:
				return '1';
			case self::AMBIENTE_HOMOLOGACAO:
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
			case self::FINALIDADE_NORMAL:
				return '1';
			case self::FINALIDADE_COMPLEMENTAR:
				return '2';
			case self::FINALIDADE_AJUSTE:
				return '3';
			case self::FINALIDADE_RETORNO:
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
	public function getPresenca($normalize = false) {
		if(!$normalize)
			return $this->presenca;
		switch ($this->presenca) {
			case self::PRESENCA_NENHUM:
				return '0';
			case self::PRESENCA_PRESENCIAL:
				return '1';
			case self::PRESENCA_INTERNET:
				return '2';
			case self::PRESENCA_TELEATENDIMENTO:
				return '3';
			case self::PRESENCA_ENTREGA:
				return '4';
			case self::PRESENCA_OUTROS:
				return '9';
		}
		return $this->presenca;
	}

	public function setPresenca($presenca) {
		$this->presenca = $presenca;
		return $this;
	}

	public function toArray() {
		$nf = array();
		$nf['id'] = $this->getID();
		$nf['numero'] = $this->getNumero();
		$nf['emitente'] = $this->getEmitente();
		$nf['cliente'] = $this->getCliente();
		$nf['produtos'] = $this->getProdutos();
		$nf['transporte'] = $this->getTransporte();
		$nf['pagamentos'] = $this->getPagamentos();
		$nf['consulta_url'] = $this->getConsultaURL();
		$nf['qrcode_data'] = $this->getQrcodeData();
		$nf['data_saida'] = $this->getDataSaida();
		$nf['data_entrada'] = $this->getDataEntrada();
		$nf['modelo'] = $this->getModelo();
		$nf['tipo'] = $this->getTipo();
		$nf['destino'] = $this->getDestino();
		$nf['natureza'] = $this->getNatureza();
		$nf['codigo'] = $this->getCodigo();
		$nf['indicador'] = $this->getIndicador();
		$nf['data_emissao'] = $this->getDataEmissao();
		$nf['serie'] = $this->getSerie();
		$nf['formato'] = $this->getFormato();
		$nf['emissao'] = $this->getEmissao();
		$nf['digito_verificador'] = $this->getDigitoVerificador();
		$nf['ambiente'] = $this->getAmbiente();
		$nf['finalidade'] = $this->getFinalidade();
		$nf['consumidor_final'] = $this->getConsumidorFinal();
		$nf['presenca'] = $this->getPresenca();
		return $nf;
	}

	public function fromArray($nf = array()) {
		if($nf instanceof NF)
			$nf = $nf->toArray();
		else if(!is_array($nf))
			return $this;
		$this->setID($nf['id']);
		$this->setNumero($nf['numero']);
		$this->setEmitente($nf['emitente']);
		if(is_null($this->getEmitente()))
			$this->setEmitente(new Emitente());
		$this->setCliente($nf['cliente']);
		if(is_null($this->getCliente()))
			$this->setCliente(new Cliente());
		$this->setProdutos($nf['produtos']);
		if(is_null($this->getProdutos()))
			$this->setProdutos(array());
		$this->setTransporte($nf['transporte']);
		if(is_null($this->getTransporte()))
			$this->setTransporte(new Transporte());
		$this->setPagamentos($nf['pagamentos']);
		if(is_null($this->getPagamentos()))
			$this->setPagamentos(array());
		$this->setConsultaURL($nf['consulta_url']);
		$this->setQrcodeData($nf['qrcode_data']);
		$this->setDataSaida($nf['data_saida']);
		$this->setDataEntrada($nf['data_entrada']);
		$this->setModelo($nf['modelo']);
		$this->setTipo($nf['tipo']);
		if(is_null($this->getTipo()))
			$this->setTipo(self::TIPO_SAIDA);
		$this->setDestino($nf['destino']);
		if(is_null($this->getDestino()))
			$this->setDestino(self::DESTINO_INTERNA);
		$this->setNatureza($nf['natureza']);
		if(is_null($this->getNatureza()))
			$this->setNatureza('VENDA PARA CONSUMIDOR FINAL');
		$this->setCodigo($nf['codigo']);
		$this->setIndicador($nf['indicador']);
		if(is_null($this->getIndicador()))
			$this->setIndicador(self::INDICADOR_AVISTA);
		$this->setDataEmissao($nf['data_emissao']);
		$this->setSerie($nf['serie']);
		$this->setFormato($nf['formato']);
		if(is_null($this->getFormato()))
			$this->setFormato(self::FORMATO_NENHUMA);
		$this->setEmissao($nf['emissao']);
		if(is_null($this->getEmissao()))
			$this->setEmissao(self::EMISSAO_NORMAL);
		$this->setDigitoVerificador($nf['digito_verificador']);
		$this->setAmbiente($nf['ambiente']);
		if(is_null($this->getAmbiente()))
			$this->setAmbiente(self::AMBIENTE_HOMOLOGACAO);
		$this->setFinalidade($nf['finalidade']);
		if(is_null($this->getFinalidade()))
			$this->setFinalidade(self::FINALIDADE_NORMAL);
		$this->setConsumidorFinal($nf['consumidor_final']);
		if(is_null($this->getConsumidorFinal()))
			$this->setConsumidorFinal('Y');
		$this->setPresenca($nf['presenca']);
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
            $this->getEmissao(true),
            $this->getCodigo()
        );
        return $id.Util::getDAC($id, 11);

	}

	public function getNodeTotal($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'total':$name);

		// Totais referentes ao ICMS
		$icms = $dom->createElement('ICMSTot');
		$total = array();
		$_produtos = $this->getProdutos();
		foreach ($_produtos as $_produto) {
			$total['produtos'] += $_produto->getPreco();
			$total['descontos'] += $_produto->getDesconto();
			$total['frete'] += $_produto->getFrete();
			$total['seguro'] += $_produto->getSeguro();
			$total['outros'] += $_produto->getDespesas();
			$total['nota'] += $_produto->getContabilizado();
			$_impostos = $_produto->getImpostos();
			foreach ($_impostos as $_imposto) {
				$total['tributos'] += $_imposto->getTotal();
				switch ($_imposto->getGrupo()) {
					case Imposto::GRUPO_ICMS:
						if($_imposto instanceof \Imposto\ICMS\Cobranca) {
							$total['icms'] += $_imposto->getNormal()->getValor();
							$total['base'] += $_imposto->getNormal()->getBase();
						}
						if($_imposto instanceof \Imposto\ICMS\Parcial) {
							$total['icms.st'] += $_imposto->getValor();
							$total['base.st'] += $_imposto->getBase();
						} else {
							$total['icms'] += $_imposto->getValor();
							$total['base'] += $_imposto->getBase();
						}
						break;
					case Imposto::GRUPO_PIS:
						$total['pis'] += $_imposto->getValor();
						break;
					case Imposto::GRUPO_COFINS:
						$total['cofins'] += $_imposto->getValor();
						break;
					case Imposto::GRUPO_IPI:
						$total['ipi'] += $_imposto->getValor();
						break;
				}
			}
		}
		$icms->appendChild($dom->createElement('vBC', Util::toCurrency($total['base'])));
		$icms->appendChild($dom->createElement('vICMS', Util::toCurrency($total['icms'])));
		$icms->appendChild($dom->createElement('vBCST', Util::toCurrency($total['base.st'])));
		$icms->appendChild($dom->createElement('vST', Util::toCurrency($total['icms.st'])));
		$icms->appendChild($dom->createElement('vProd', Util::toCurrency($total['produtos'])));
		$icms->appendChild($dom->createElement('vFrete', Util::toCurrency($total['frete'])));
		$icms->appendChild($dom->createElement('vSeg', Util::toCurrency($total['seguro'])));
		$icms->appendChild($dom->createElement('vDesc', Util::toCurrency($total['descontos'])));
		$icms->appendChild($dom->createElement('vII', Util::toCurrency($total['ii'])));
		$icms->appendChild($dom->createElement('vIPI', Util::toCurrency($total['ipi'])));
		$icms->appendChild($dom->createElement('vPIS', Util::toCurrency($total['pis'])));
		$icms->appendChild($dom->createElement('vCOFINS', Util::toCurrency($total['cofins'])));
		$icms->appendChild($dom->createElement('vOutro', Util::toCurrency($total['outros'])));
		$icms->appendChild($dom->createElement('vNF', Util::toCurrency($total['nota'])));
		$icms->appendChild($dom->createElement('vTotTrib', Util::toCurrency($total['tributos'])));
		$icms->appendChild($dom->createElement('vICMSDeson', Util::toCurrency($total['desoneracao'])));
		$element->appendChild($icms);

		// TODO: Totais referentes ao ISSQN

		// TODO: Retenção de Tributos Federais
		return $element;
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

		$db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
		$this->getEmitente()->getEndereco()->checkCodigos();
		$municipio = $this->getEmitente()->getEndereco()->getMunicipio();
		$estado = $municipio->getEstado();
		$ident = $dom->createElement('ide');
		$ident->appendChild($dom->createElement('cUF', $estado->getCodigo(true)));
		$ident->appendChild($dom->createElement('cNF', $this->getCodigo(true)));
		$ident->appendChild($dom->createElement('natOp', $this->getNatureza(true)));
		$ident->appendChild($dom->createElement('indPag', $this->getIndicador(true)));
		$ident->appendChild($dom->createElement('mod', $this->getModelo(true)));
		$ident->appendChild($dom->createElement('serie', $this->getSerie(true)));
		$ident->appendChild($dom->createElement('nNF', $this->getNumero(true)));
		$ident->appendChild($dom->createElement('dhEmi', $this->getDataEmissao(true)));
		$ident->appendChild($dom->createElement('tpNF', $this->getTipo(true)));
		$ident->appendChild($dom->createElement('idDest', $this->getDestino(true)));
		$ident->appendChild($dom->createElement('cMunFG', $municipio->getCodigo(true)));
		$ident->appendChild($dom->createElement('tpImp', $this->getFormato(true)));
		$ident->appendChild($dom->createElement('tpEmis', $this->getEmissao(true)));
		$ident->appendChild($dom->createElement('cDV', $this->getDigitoVerificador(true)));
		$ident->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
		$ident->appendChild($dom->createElement('finNFe', $this->getFinalidade(true)));
		$ident->appendChild($dom->createElement('indFinal', $this->getConsumidorFinal(true)));
		$ident->appendChild($dom->createElement('indPres', $this->getPresenca(true)));
		$ident->appendChild($dom->createElement('procEmi', 0));
		$ident->appendChild($dom->createElement('verProc', self::VERSAO));

		$info->appendChild($ident);

		$emitente = $this->getEmitente()->getNode();
		$emitente = $dom->importNode($emitente, true);
		$info->appendChild($emitente);
		$cliente = $this->getCliente()->getNode();
		$cliente = $dom->importNode($cliente, true);
		$info->appendChild($cliente);
		$item = 0;
		$_produtos = $this->getProdutos();
		foreach ($_produtos as $_produto) {
			if(is_null($_produto->getItem())) {
				$item += 1;
				$_produto->setItem($item);
			} else
				$item = $_produto->getItem();
			$produto = $_produto->getNode();
			$produto = $dom->importNode($produto, true);
			$info->appendChild($produto);
		}
		$total = $this->getNodeTotal();
		$total = $dom->importNode($total, true);
		$info->appendChild($total);
		$transporte = $this->getTransporte()->getNode();
		$transporte = $dom->importNode($transporte, true);
		$info->appendChild($transporte);
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
