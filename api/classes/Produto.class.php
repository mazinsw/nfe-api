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

class ProdutoUnidade {
	const UNIDADE = 'unidade';
	const PECA = 'peca';
	const METRO = 'metro';
	const GRAMA = 'grama';
	const LITRO = 'litro';
}

/**
 * Produto ou serviço que está sendo vendido ou prestado e será adicionado
 * na nota fiscal
 */
class Produto implements NodeInterface {

	private $item;
	private $codigo;
	private $codigo_tributario;
	private $codigo_barras;
	private $descricao;
	private $unidade;
	private $multiplicador;
	private $peso_liquido;
	private $peso_bruto;
	private $preco;
	private $quantidade;
	private $desconto;
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
			case ProdutoUnidade::UNIDADE:
				return 'UN';
			case ProdutoUnidade::PECA:
				return 'PC';
			case ProdutoUnidade::METRO:
				return 'm';
			case ProdutoUnidade::GRAMA:
				return 'g';
			case ProdutoUnidade::LITRO:
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

	public function getPesoLiquido($normalize = false) {
		if(!$normalize)
			return $this->peso_liquido;
		return Util::toFloat($this->peso_liquido);
	}

	public function setPesoLiquido($peso_liquido) {
		$this->peso_liquido = $peso_liquido;
		return $this;
	}

	public function getPesoBruto($normalize = false) {
		if(!$normalize)
			return $this->peso_bruto;
		return Util::toFloat($this->peso_bruto);
	}

	public function setPesoBruto($peso_bruto) {
		$this->peso_bruto = $peso_bruto;
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

	public function toArray() {
		$produto = array();
		$produto['item'] = $this->getItem();
		$produto['codigo'] = $this->getCodigo();
		$produto['codigo_tributario'] = $this->getCodigoTributario();
		$produto['codigo_barras'] = $this->getCodigoBarras();
		$produto['descricao'] = $this->getDescricao();
		$produto['unidade'] = $this->getUnidade();
		$produto['multiplicador'] = $this->getMultiplicador();
		$produto['peso_liquido'] = $this->getPesoLiquido();
		$produto['peso_bruto'] = $this->getPesoBruto();
		$produto['preco'] = $this->getPreco();
		$produto['quantidade'] = $this->getQuantidade();
		$produto['desconto'] = $this->getDesconto();
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
		$this->setCodigo($produto['codigo']);
		$this->setCodigoTributario($produto['codigo_tributario']);
		$this->setCodigoBarras($produto['codigo_barras']);
		$this->setDescricao($produto['descricao']);
		$this->setUnidade($produto['unidade']);
		$this->setMultiplicador($produto['multiplicador']);
		if(is_null($this->getMultiplicador()))
			$this->setMultiplicador(1);
		$this->setPesoLiquido($produto['peso_liquido']);
		$this->setPesoBruto($produto['peso_bruto']);
		$this->setPreco($produto['preco']);
		$this->setQuantidade($produto['quantidade']);
		$this->setDesconto($produto['desconto']);
		$this->setCFOP($produto['cfop']);
		$this->setNCM($produto['ncm']);
		$this->setCEST($produto['cest']);
		$this->setImpostos($produto['impostos']);
		if(is_null($this->getImpostos()))
			$this->setImpostos(array());
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'det':$name);
		$attr = $dom->createAttribute('nItem');
		$attr->value = $this->getItem(true);
		$element->appendChild($attr);

		$produto = $dom->createElement('prod');
		$produto->appendChild($dom->createElement('nItemPed', $this->getItem(true)));
		$produto->appendChild($dom->createElement('cProd', $this->getCodigo(true)));
		$produto->appendChild($dom->createElement('cEAN', $this->getCodigoBarras(true)));
		$produto->appendChild($dom->createElement('xProd', $this->getDescricao(true)));
		$produto->appendChild($dom->createElement('NCM', $this->getNCM(true)));
//		$produto->appendChild($dom->createElement('NVE', $this->getNVE(true)));
		$produto->appendChild($dom->createElement('CEST', $this->getCEST(true)));
//		$produto->appendChild($dom->createElement('EXTIPI', $this->getEXTIPI(true)));
		$produto->appendChild($dom->createElement('CFOP', $this->getCFOP(true)));
		$produto->appendChild($dom->createElement('uCom', $this->getUnidade(true)));
		$produto->appendChild($dom->createElement('qCom', $this->getQuantidade(true)));
		$produto->appendChild($dom->createElement('vUnCom', $this->getPreco(true)));
		$produto->appendChild($dom->createElement('vProd', $this->getPreco(true)));
		$produto->appendChild($dom->createElement('cEANTrib', $this->getCodigoTributario(true)));
		$produto->appendChild($dom->createElement('uTrib', $this->getUnidade(true)));
		$produto->appendChild($dom->createElement('qTrib', $this->getQuantidade(true)));
		$produto->appendChild($dom->createElement('vUnTrib', $this->getPreco(true)));
//		$produto->appendChild($dom->createElement('vFrete', $this->getFrete(true)));
//		$produto->appendChild($dom->createElement('vSeg', $this->getSeguro(true)));
		$produto->appendChild($dom->createElement('vDesc', $this->getDesconto(true)));
//		$produto->appendChild($dom->createElement('vOutro', $this->getDespesas(true)));
		$produto->appendChild($dom->createElement('indTot', $this->getMultiplicador(true)));
//		$produto->appendChild($dom->createElement('DI', $this->getImportacoes(true)));
//		$produto->appendChild($dom->createElement('detExport', $this->getDetalhes(true)));
//		$produto->appendChild($dom->createElement('xPed', $this->getPedido(true)));
//		$produto->appendChild($dom->createElement('nFCI', $this->getControle(true)));
		$element->appendChild($produto);

		$imposto = $dom->createElement('imposto');
		$_impostos = $this->getImpostos();
		$imposto_total = 0.00;
		$imposto_fed = 0.00;
		$imposto_est = 0.00;
		$imposto_mun = 0.00;
		$grupos = array();
		foreach ($_impostos as $_imposto) {
			$_imposto->setBase($this->getPreco());
			$tipo = $_imposto->getTipo();
			$_imposto->setTipo(ImpostoTipo::TODOS);
			$imposto_total += $_imposto->getValor();
			$_imposto->setTipo(ImpostoTipo::FEDERAL);
			$imposto_fed += $_imposto->getValor();
			$_imposto->setTipo(ImpostoTipo::ESTADUAL);
			$imposto_est += $_imposto->getValor();
			$_imposto->setTipo(ImpostoTipo::MUNICIPAL);
			$imposto_mun += $_imposto->getValor();
			$_imposto->setTipo($tipo);
			$grupos[$_imposto->getGrupo(true)][] = $_imposto;
		}
		// TODO: verificar se é obrigatório informar o total dos tributos
		$imp_total = $dom->createElement('vTotTrib', Util::toCurrency($imposto_total));
		$imposto->appendChild($imp_total);
		foreach ($grupos as $tag => $_grupo) {
			$grupo = $dom->createElement($tag);
			foreach ($_grupo as $_imposto) {
				$node = $_imposto->getNode();
				$grupo->appendChild($node);
			}
			$imposto->appendChild($grupo);
		}
		$element->appendChild($imposto);
		// TODO: verificar se é obrigatório a informação adicional abaixo
		$_info = array();
		if(Util::isGreater($imposto_fed, 0.00))
			$_info[] = Util::toMoney($imposto_fed).' Federal';
		if(Util::isGreater($imposto_est, 0.00))
			$_info[] = Util::toMoney($imposto_est).' Estadual';
		if(Util::isGreater($imposto_mun, 0.00))
			$_info[] = Util::toMoney($imposto_mun).' Municipal';
		if(count($_info) > 0) {
			$info_str = 'Trib. aprox.: '.implode(', ', $_info);
			$info = $dom->createElement('infAdProd', $info_str);
			$element->appendChild($info);
		}
		return $element;
	}

}