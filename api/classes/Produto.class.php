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
 * Produto ou serviço que está sendo vendido ou prestado e será adicionado
 * na nota fiscal
 */
class Produto implements NodeInterface {

	/**
	 * Unidade do produto, Não informar a grandeza
	 */
	const UNIDADE_UNIDADE = 'unidade';
	const UNIDADE_PECA = 'peca';
	const UNIDADE_METRO = 'metro';
	const UNIDADE_GRAMA = 'grama';
	const UNIDADE_LITRO = 'litro';

	private $item;
	private $pedido;
	private $codigo;
	private $codigo_tributario;
	private $codigo_barras;
	private $descricao;
	private $unidade;
	private $multiplicador;
	private $preco;
	private $quantidade;
	private $tributada;
	private $peso;
	private $desconto;
	private $seguro;
	private $frete;
	private $despesas;
	private $excecao;
	private $cfop;
	private $ncm;
	private $cest;
	private $impostos;

	public function __construct($produto = array()) {
		$this->fromArray($produto);
	}

	/**
	 * Número do Item do Pedido de Compra - Identificação do número do item do
	 * pedido de Compra
	 */
	public function getItem($normalize = false) {
		if(!$normalize)
			return $this->item;
		return $this->item;
	}

	public function setItem($item) {
		$this->item = $item;
		return $this;
	}

	/**
	 * informar o número do pedido de compra, o campo é de livre uso do emissor
	 */
	public function getPedido($normalize = false) {
		if(!$normalize)
			return $this->pedido;
		return $this->pedido;
	}

	public function setPedido($pedido) {
		$this->pedido = $pedido;
		return $this;
	}

	/**
	 * Código do produto ou serviço. Preencher com CFOP caso se trate de itens
	 * não relacionados com mercadorias/produto e que o contribuinte não possua
	 * codificação própria
	 * Formato ”CFOP9999”.
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
	 * Código do produto ou serviço. Preencher com CFOP caso se trate de itens
	 * não relacionados com mercadorias/produto e que o contribuinte não possua
	 * codificação própria
	 * Formato ”CFOP9999”.
	 */
	public function getCodigoTributario($normalize = false) {
		if(!$normalize)
			return $this->codigo_tributario;
		return $this->codigo_tributario;
	}

	public function setCodigoTributario($codigo_tributario) {
		$this->codigo_tributario = $codigo_tributario;
		return $this;
	}

	/**
	 * GTIN (Global Trade Item Number) do produto, antigo código EAN ou código
	 * de barras
	 */
	public function getCodigoBarras($normalize = false) {
		if(!$normalize)
			return $this->codigo_barras;
		return $this->codigo_barras;
	}

	public function setCodigoBarras($codigo_barras) {
		$this->codigo_barras = $codigo_barras;
		return $this;
	}

	/**
	 * Descrição do produto ou serviço
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
	 * Unidade do produto, Não informar a grandeza
	 */
	public function getUnidade($normalize = false) {
		if(!$normalize)
			return $this->unidade;
		switch ($this->unidade) {
			case self::UNIDADE_UNIDADE:
				return 'UN';
			case self::UNIDADE_PECA:
				return 'PC';
			case self::UNIDADE_METRO:
				return 'm';
			case self::UNIDADE_GRAMA:
				return 'g';
			case self::UNIDADE_LITRO:
				return 'L';
		}
		return $this->unidade;
	}

	public function setUnidade($unidade) {
		$this->unidade = $unidade;
		return $this;
	}

	public function getMultiplicador($normalize = false) {
		if(!$normalize)
			return $this->multiplicador;
		return $this->multiplicador;
	}

	public function setMultiplicador($multiplicador) {
		$this->multiplicador = $multiplicador;
		return $this;
	}

	/**
	 * Valor unitário de comercialização  - alterado para aceitar 0 a 10 casas
	 * decimais e 11 inteiros
	 */
	public function getPreco($normalize = false) {
		if(!$normalize)
			return $this->preco;
		return Util::toCurrency($this->preco);
	}

	public function setPreco($preco) {
		$this->preco = $preco;
		return $this;
	}

	/**
	 * Quantidade Comercial  do produto, alterado para aceitar de 0 a 4 casas
	 * decimais e 11 inteiros.
	 */
	public function getQuantidade($normalize = false) {
		if(!$normalize)
			return $this->quantidade;
		return Util::toFloat($this->quantidade);
	}

	public function setQuantidade($quantidade) {
		$this->quantidade = $quantidade;
		return $this;
	}

	/**
	 * Informa a quantidade tributada
	 */
	public function getTributada($normalize = false) {
		if(!$normalize)
			return is_null($this->tributada)?$this->getQuantidade():$this->tributada;
		return Util::toFloat($this->getTributada());
	}

	public function setTributada($tributada) {
		$this->tributada = $tributada;
		return $this;
	}

	public function getPeso() {
		return $this->peso;
	}

	public function setPeso($peso) {
		$this->peso = $peso;
		return $this;
	}

	/**
	 * Valor do Desconto
	 */
	public function getDesconto($normalize = false) {
		if(!$normalize)
			return $this->desconto;
		return Util::toCurrency($this->desconto);
	}

	public function setDesconto($desconto) {
		$this->desconto = $desconto;
		return $this;
	}

	/**
	 * informar o valor do Seguro, o Seguro deve ser rateado entre os itens de
	 * produto
	 */
	public function getSeguro($normalize = false) {
		if(!$normalize)
			return $this->seguro;
		return Util::toCurrency($this->seguro);
	}

	public function setSeguro($seguro) {
		$this->seguro = $seguro;
		return $this;
	}

	/**
	 * informar o valor do Frete, o Frete deve ser rateado entre os itens de
	 * produto.
	 */
	public function getFrete($normalize = false) {
		if(!$normalize)
			return $this->frete;
		return Util::toCurrency($this->frete);
	}

	public function setFrete($frete) {
		$this->frete = $frete;
		return $this;
	}

	/**
	 * informar o valor de outras despesas acessórias do item de produto ou
	 * serviço
	 */
	public function getDespesas($normalize = false) {
		if(!$normalize)
			return $this->despesas;
		return Util::toCurrency($this->despesas);
	}

	public function setDespesas($despesas) {
		$this->despesas = $despesas;
		return $this;
	}

	/**
	 * Código EX TIPI
	 */
	public function getExcecao($normalize = false) {
		if(!$normalize)
			return $this->excecao;
		return Util::padDigit($this->excecao, 2);
	}

	public function setExcecao($excecao) {
		$this->excecao = $excecao;
		return $this;
	}

	public function getCFOP($normalize = false) {
		if(!$normalize)
			return $this->cfop;
		return $this->cfop;
	}

	public function setCFOP($cfop) {
		$this->cfop = $cfop;
		return $this;
	}

	/**
	 * Código NCM (8 posições), será permitida a informação do gênero (posição
	 * do capítulo do NCM) quando a operação não for de comércio exterior
	 * (importação/exportação) ou o produto não seja tributado pelo IPI. Em
	 * caso de item de serviço ou item que não tenham produto (Ex.
	 * transferência de crédito, crédito do ativo imobilizado, etc.), informar
	 * o código 00 (zeros) (v2.0)
	 */
	public function getNCM($normalize = false) {
		if(!$normalize)
			return $this->ncm;
		return $this->ncm;
	}

	public function setNCM($ncm) {
		$this->ncm = $ncm;
		return $this;
	}

	public function getCEST($normalize = false) {
		if(!$normalize)
			return $this->cest;
		return $this->cest;
	}

	public function setCEST($cest) {
		$this->cest = $cest;
		return $this;
	}

	public function getImpostos() {
		return $this->impostos;
	}

	public function setImpostos($impostos) {
		$this->impostos = $impostos;
		return $this;
	}

	public function addImposto($imposto) {
		$this->impostos[] = $imposto;
		return $this;
	}

	/**
	 * Valor unitário
	 */
	public function getPrecoUnitario($normalize = false) {
		if(!$normalize)
			return $this->getPreco() / $this->getQuantidade();
		return Util::toCurrency($this->getPrecoUnitario());
	}

	/**
	 * Valor tributável
	 */
	public function getPrecoTributavel($normalize = false) {
		if(!$normalize)
			return $this->getPreco() / $this->getTributada();
		return Util::toCurrency($this->getPrecoTributavel());
	}

	public function getBase($normalize = false) {
		if(!$normalize)
			return $this->getPreco() - $this->getDesconto();
		return Util::toCurrency($this->getBase());
	}

	public function getContabilizado($normalize = false) {
		if(!$normalize)
			return $this->getBase() * $this->getMultiplicador();
		return Util::toCurrency($this->getContabilizado());
	}

	public function getImpostoInfo() {
		$config = SEFAZ::getInstance()->getConfiguracao();
		$db = $config->getBanco();
		$endereco = $config->getEmitente()->getEndereco();
		$info = array('total' => 0.00);
		$tipos = array(
			// Imposto::TIPO_IMPORTADO, // TODO: determinar quando usar
			Imposto::TIPO_NACIONAL,
			Imposto::TIPO_ESTADUAL,
			Imposto::TIPO_MUNICIPAL
		);
		$imposto = new \Imposto\Total();
		$imposto->setBase($this->getBase());
		$aliquota = $db->getImpostoAliquota($this->getNCM(), $endereco->getMunicipio()->getEstado()->getUF(),
			$this->getExcecao(), $config->getEmitente()->getCNPJ(), $config->getTokenIBPT());
		if($aliquota === false)
			throw new Exception('Não foi possível obter o tributo aproximado do produto "'.$this->getDescricao().'" e item '.$this->getItem(), 404);
		foreach ($tipos as $tipo) {
			$imposto->setAliquota($aliquota[$tipo]);
			$tributo = round($imposto->getTotal(), 2);
			$info[$tipo] = $tributo;
			$info['total'] += $tributo;
		}
		$info['info'] = $aliquota['info'];
		return $info;
	}

	public function toArray() {
		$produto = array();
		$produto['item'] = $this->getItem();
		$produto['pedido'] = $this->getPedido();
		$produto['codigo'] = $this->getCodigo();
		$produto['codigo_tributario'] = $this->getCodigoTributario();
		$produto['codigo_barras'] = $this->getCodigoBarras();
		$produto['descricao'] = $this->getDescricao();
		$produto['unidade'] = $this->getUnidade();
		$produto['multiplicador'] = $this->getMultiplicador();
		$produto['preco'] = $this->getPreco();
		$produto['quantidade'] = $this->getQuantidade();
		$produto['tributada'] = $this->getTributada();
		$produto['peso'] = $this->getPeso();
		$produto['desconto'] = $this->getDesconto();
		$produto['seguro'] = $this->getSeguro();
		$produto['frete'] = $this->getFrete();
		$produto['despesas'] = $this->getDespesas();
		$produto['excecao'] = $this->getExcecao();
		$produto['cfop'] = $this->getCFOP();
		$produto['ncm'] = $this->getNCM();
		$produto['cest'] = $this->getCEST();
		$produto['impostos'] = $this->getImpostos();
		return $produto;
	}

	public function fromArray($produto = array()) {
		if($produto instanceof Produto)
			$produto = $produto->toArray();
		else if(!is_array($produto))
			return $this;
		$this->setItem($produto['item']);
		$this->setPedido($produto['pedido']);
		$this->setCodigo($produto['codigo']);
		$this->setCodigoTributario($produto['codigo_tributario']);
		$this->setCodigoBarras($produto['codigo_barras']);
		$this->setDescricao($produto['descricao']);
		$this->setUnidade($produto['unidade']);
		if(is_null($this->getUnidade()))
			$this->setUnidade(self::UNIDADE_UNIDADE);
		$this->setMultiplicador($produto['multiplicador']);
		if(is_null($this->getMultiplicador()))
			$this->setMultiplicador(1);
		$this->setPreco($produto['preco']);
		$this->setQuantidade($produto['quantidade']);
		$this->setTributada($produto['tributada']);
		$this->setPeso($produto['peso']);
		if(is_null($this->getPeso()))
			$this->setPeso(new Peso());
		$this->setDesconto($produto['desconto']);
		$this->setSeguro($produto['seguro']);
		$this->setFrete($produto['frete']);
		$this->setDespesas($produto['despesas']);
		$this->setExcecao($produto['excecao']);
		$this->setCFOP($produto['cfop']);
		$this->setNCM($produto['ncm']);
		$this->setCEST($produto['cest']);
		$this->setImpostos($produto['impostos']);
		if(is_null($this->getImpostos()))
			$this->setImpostos(array());
		return $this;
	}

	public static function addNodeInformacoes($tributos, $element, $name = null) {
		$detalhes = array();
		$formatos = array(
			Imposto::TIPO_IMPORTADO => '%s Importado',
			Imposto::TIPO_NACIONAL => '%s Federal',
			Imposto::TIPO_ESTADUAL => '%s Estadual',
			Imposto::TIPO_MUNICIPAL => '%s Municipal'
		);
		foreach ($formatos as $tipo => $formato) {
			if(!Util::isGreater($tributos[$tipo], 0.00))
				continue;
			$detalhes[] = sprintf($formato, Util::toMoney($tributos[$tipo]));
		}
		if(count($detalhes) == 0)
			return;
		$dom = $element->ownerDocument;
		$fonte = 'Fonte: '.$tributos['info']['fonte'].' '.$tributos['info']['chave'];
		$ultimo = '';
		if(count($detalhes) > 1)
			$ultimo = ' e '.array_pop($detalhes);
		$texto = 'Trib. aprox.: '.implode(', ', $detalhes).$ultimo.'. '.$fonte;
		$info = $dom->createElement(is_null($name)?'infAdProd':$name, $texto);
		$element->appendChild($info);
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'det':$name);
		$attr = $dom->createAttribute('nItem');
		$attr->value = $this->getItem(true);
		$element->appendChild($attr);

		$produto = $dom->createElement('prod');
		$produto->appendChild($dom->createElement('cProd', $this->getCodigo(true)));
		$produto->appendChild($dom->createElement('cEAN', $this->getCodigoBarras(true)));
		$produto->appendChild($dom->createElement('xProd', $this->getDescricao(true)));
		$produto->appendChild($dom->createElement('NCM', $this->getNCM(true)));
//		$produto->appendChild($dom->createElement('NVE', $this->getNVE(true)));
		$produto->appendChild($dom->createElement('CEST', $this->getCEST(true)));
		if(!is_null($this->getExcecao())) {
			$produto->appendChild($dom->createElement('EXTIPI', $this->getExcecao(true)));
		}
		$produto->appendChild($dom->createElement('CFOP', $this->getCFOP(true)));
		$produto->appendChild($dom->createElement('uCom', $this->getUnidade(true)));
		$produto->appendChild($dom->createElement('qCom', $this->getQuantidade(true)));
		$produto->appendChild($dom->createElement('vUnCom', $this->getPrecoUnitario(true)));
		$produto->appendChild($dom->createElement('vProd', $this->getPreco(true)));
		$produto->appendChild($dom->createElement('cEANTrib', $this->getCodigoTributario(true)));
		$produto->appendChild($dom->createElement('uTrib', $this->getUnidade(true)));
		$produto->appendChild($dom->createElement('qTrib', $this->getTributada(true)));
		$produto->appendChild($dom->createElement('vUnTrib', $this->getPrecoTributavel(true)));
		if(!is_null($this->getFrete()))
			$produto->appendChild($dom->createElement('vFrete', $this->getFrete(true)));
		if(!is_null($this->getSeguro()))
			$produto->appendChild($dom->createElement('vSeg', $this->getSeguro(true)));
		if(!is_null($this->getDesconto()))
			$produto->appendChild($dom->createElement('vDesc', $this->getDesconto(true)));
		if(!is_null($this->getDespesas()))
			$produto->appendChild($dom->createElement('vOutro', $this->getDespesas(true)));
		$produto->appendChild($dom->createElement('indTot', $this->getMultiplicador(true)));
//		$produto->appendChild($dom->createElement('DI', $this->getImportacoes(true)));
//		$produto->appendChild($dom->createElement('detExport', $this->getDetalhes(true)));
		$produto->appendChild($dom->createElement('xPed', $this->getPedido(true)));
		$produto->appendChild($dom->createElement('nItemPed', $this->getItem(true)));
//		$produto->appendChild($dom->createElement('nFCI', $this->getControle(true)));
		$element->appendChild($produto);

		$imposto = $dom->createElement('imposto');
		$grupos = array();
		$_impostos = $this->getImpostos();
		foreach ($_impostos as $_imposto) {
			if(is_null($_imposto->getBase()))
				$_imposto->setBase($this->getBase());
			$grupos[$_imposto->getGrupo(true)][] = $_imposto;
		}
		$imposto_info = $this->getImpostoInfo();
		$imp_total = $dom->createElement('vTotTrib', Util::toCurrency($imposto_info['total']));
		$imposto->appendChild($imp_total);
		foreach ($grupos as $tag => $_grupo) {
			$grupo = $dom->createElement($tag);
			foreach ($_grupo as $_imposto) {
				$node = $_imposto->getNode();
				$node = $dom->importNode($node, true);
				$grupo->appendChild($node);
			}
			$imposto->appendChild($grupo);
		}
		$element->appendChild($imposto);
		// TODO: verificar se é obrigatório a informação adicional abaixo
		self::addNodeInformacoes($imposto_info, $element);
		return $element;
	}

}