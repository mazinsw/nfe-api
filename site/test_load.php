<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();

$nfce = new NFCe();
$nfce->setCodigo('77882192');
$nfce->setSerie('1');
$nfce->setNumero('81');
$nfce->setDataEmissao(strtotime('2016-09-16T21:36:03-03:00'));
$nfce->setPresenca(NF::PRESENCA_PRESENCIAL);

/* Emitente */
$emitente = new Emitente();
$emitente->setRazaoSocial('Empresa LTDA');
$emitente->setFantasia('Minha Empresa');
$emitente->setCNPJ('08380787000176');
$emitente->setTelefone('11955886644');
$emitente->setIE('123456789');
$emitente->setIM('95656');
$emitente->setRegime(Emitente::REGIME_SIMPLES);

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->getEstado()
		 ->setUF('PR');
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$emitente->setEndereco($endereco);
$sefaz->getConfiguracao()->setEmitente($emitente);
$nfce->setEmitente($emitente);

/* Destinatário */
$cliente = new Cliente();
$cliente->setNome('Fulano da Silva');
$cliente->setCPF('12345678912');
$cliente->setEmail('fulano@site.com.br');
$cliente->setTelefone('11988220055');

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->getEstado()
		 ->setUF('PR');
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$cliente->setEndereco($endereco);
$nfce->setCliente($cliente);

/* Produtos */
$produto = new Produto();
$produto->setCodigo(123456);
$produto->setCodigoBarras('7894900011517');
$produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
$produto->setUnidade(Produto::UNIDADE_UNIDADE);
$produto->setPreco(230.00);
$produto->setQuantidade(1);
$produto->setNCM('22021000');
$produto->setCEST('0300700');
$produto->setCFOP('5405');
/* Impostos */
$imposto = new \Imposto\ICMS\Cobrado();
$imposto->setBase(0.00);
$produto->addImposto($imposto);

$imposto = new \Imposto\PIS\Aliquota();
$imposto->setTributacao(\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
$imposto->setAliquota(0.65);
$produto->addImposto($imposto);

$imposto = new \Imposto\COFINS\Aliquota();
$imposto->setTributacao(\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
$imposto->setAliquota(3.00);
$produto->addImposto($imposto);

$nfce->addProduto($produto);

$produto = new Produto();
$produto->setCodigo(123456);
$produto->setCodigoBarras('7894900011523');
$produto->setDescricao('REFRIGERANTE FANTA LARANJA 2L');
$produto->setUnidade(Produto::UNIDADE_UNIDADE);
$produto->setPreco(55.00);
$produto->setQuantidade(1);
$produto->setDesconto(2.20);
$produto->setNCM('22021000');
$produto->setCEST('0300700');
$produto->setCFOP('5405');
/* Impostos */
$impostos = array();

/* ICMS */
$icms_integral = new \Imposto\ICMS\Integral();
$icms_integral->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_integral->setBase(588.78);
$icms_integral->setAliquota(18.00);
$impostos[] = $icms_integral;

$icms_cobranca = new \Imposto\ICMS\Cobranca();
$icms_cobranca->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_cobranca->getNormal()->setBase(100.00);
$icms_cobranca->getNormal()->setAliquota(18.00);
$icms_cobranca->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_cobranca->setBase(135.00);
$icms_cobranca->setMargem(50.00);
$icms_cobranca->setReducao(10.00);
$icms_cobranca->setAliquota(18.00);
$impostos[] = $icms_cobranca;

$icms_reducao = new \Imposto\ICMS\Reducao();
$icms_reducao->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_reducao->setBase(90.00);
$icms_reducao->setAliquota(18.0);
$icms_reducao->setReducao(10.0);
$impostos[] = $icms_reducao;

$icms_parcial = new \Imposto\ICMS\Parcial();
$icms_parcial->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_parcial->setBase(135.00);
$icms_parcial->setMargem(50.00);
$icms_parcial->setReducao(10.00);
$icms_parcial->setAliquota(18.00);
$impostos[] = $icms_parcial;

$icms_isenta = new \Imposto\ICMS\Isento();
$impostos[] = $icms_isenta;

$icms_isenta_cond = new \Imposto\ICMS\Isento();
$icms_isenta_cond->setDesoneracao(1800.00);
$icms_isenta_cond->setMotivo(\Imposto\ICMS\Isento::MOTIVO_TAXI);
$impostos[] = $icms_isenta_cond;

$icms_nao_trib = new \Imposto\ICMS\Isento();
$icms_nao_trib->setTributacao('41');
$impostos[] = $icms_nao_trib;

$icms_suspensao = new \Imposto\ICMS\Isento();
$icms_suspensao->setTributacao('50');
$impostos[] = $icms_suspensao;

$icms_diferido = new \Imposto\ICMS\Diferido();
$impostos[] = $icms_diferido;

$icms_diferido = new \Imposto\ICMS\Diferido();
$icms_diferido->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_diferido->setBase(80.00);
$icms_diferido->setReducao(20.00);
$icms_diferido->setAliquota(18.00);
$icms_diferido->setDiferimento(100.00);
$impostos[] = $icms_diferido;

$icms_diferido = new \Imposto\ICMS\Diferido();
$icms_diferido->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_diferido->setBase(1000.00);
$icms_diferido->setAliquota(18.00);
$icms_diferido->setDiferimento(33.3333);
$impostos[] = $icms_diferido;

$icms_cobrado = new \Imposto\ICMS\Cobrado();
$icms_cobrado->setBase(135.00);
$icms_cobrado->setValor(24.30);
$impostos[] = $icms_cobrado;

$icms_mista = new \Imposto\ICMS\Mista();
$icms_mista->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_mista->getNormal()->setBase(90.00);
$icms_mista->getNormal()->setReducao(10.00);
$icms_mista->getNormal()->setAliquota(18.00);

$icms_mista->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_mista->setBase(162.00);
$icms_mista->setMargem(100.00);
$icms_mista->setReducao(10.00);
$icms_mista->setAliquota(18.00);
$impostos[] = $icms_mista;

$icms_generico = new \Imposto\ICMS\Generico();
$impostos[] = $icms_generico;

$icms_generico = new \Imposto\ICMS\Generico();
$icms_generico->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_generico->getNormal()->setBase(90.00);
$icms_generico->getNormal()->setAliquota(18.00);
$icms_generico->getNormal()->setReducao(10.00);
$impostos[] = $icms_generico;

$icms_generico = new \Imposto\ICMS\Generico();
$icms_generico->getNormal()->setBase(90.00);
$icms_generico->getNormal()->setReducao(10.00);
$icms_generico->getNormal()->setAliquota(18.00);

$icms_generico->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_generico->setBase(162.00);
$icms_generico->setMargem(100.00);
$icms_generico->setReducao(10.00);
$icms_generico->setAliquota(18.00);
$impostos[] = $icms_generico;

$icms_generico = new \Imposto\ICMS\Generico();
$icms_generico->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_generico->getNormal()->setBase(90.00);
$icms_generico->getNormal()->setReducao(10.00);
$icms_generico->getNormal()->setAliquota(18.00);

$icms_generico->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_generico->setBase(162.00);
$icms_generico->setMargem(100.00);
$icms_generico->setReducao(10.00);
$icms_generico->setAliquota(18.00);
$impostos[] = $icms_generico;

$icms_partilha = new \Imposto\ICMS\Partilha();
$icms_partilha->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_partilha->getNormal()->setBase(90.00);
$icms_partilha->getNormal()->setReducao(10.00);
$icms_partilha->getNormal()->setAliquota(18.00);

$icms_partilha->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_partilha->setBase(162.00);
$icms_partilha->setMargem(100.00);
$icms_partilha->setReducao(10.00);
$icms_partilha->setAliquota(18.00);
$icms_partilha->setOperacao(5.70);
$icms_partilha->setUF('PR');
$impostos[] = $icms_partilha;

$icms_substituto = new \Imposto\ICMS\Substituto();
$icms_substituto->getNormal()->setBase(135.00);
$icms_substituto->getNormal()->setValor(24.30);

$icms_substituto->setBase(135.00);
$icms_substituto->setValor(24.30);
$impostos[] = $icms_substituto;

/* Simples Nacional */
$icms_cobranca = new \Imposto\ICMS\Simples\Cobranca();
$icms_cobranca->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_cobranca->getNormal()->setBase(1036.80);
$icms_cobranca->getNormal()->setAliquota(1.25);
$icms_cobranca->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_cobranca->setBase(162.00);
$icms_cobranca->setMargem(100.00);
$icms_cobranca->setReducao(10.00);
$icms_cobranca->setAliquota(18.00);
$impostos[] = $icms_cobranca;

$icms_isento = new \Imposto\ICMS\Simples\Isento();
$impostos[] = $icms_isento;

$icms_simples_normal = new \Imposto\ICMS\Simples\Normal();
$icms_simples_normal->setBase(1036.80);
$icms_simples_normal->setAliquota(1.25);
$impostos[] = $icms_simples_normal;

$icms_parcial = new \Imposto\ICMS\Simples\Parcial();
$icms_parcial->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_parcial->setBase(162.00);
$icms_parcial->setMargem(100.00);
$icms_parcial->setReducao(10.00);
$icms_parcial->setAliquota(18.00);
$impostos[] = $icms_parcial;

/* PIS */
$pis_aliquota = new \Imposto\PIS\Aliquota();
$pis_aliquota->setTributacao(\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
$pis_aliquota->setBase(883.12);
$pis_aliquota->setAliquota(1.65);
$impostos[] = $pis_aliquota;

$pis_quantidade = new \Imposto\PIS\Quantidade();
$pis_quantidade->setQuantidade(1000);
$pis_quantidade->setAliquota(0.0076);
$impostos[] = $pis_quantidade;

$pis_isento = new \Imposto\PIS\Isento();
$pis_isento->setTributacao(\Imposto\PIS\Isento::TRIBUTACAO_MONOFASICA);
$impostos[] = $pis_isento;

$pis_generico = new \Imposto\PIS\Generico();
$pis_generico->setValor(3.50);
$impostos[] = $pis_generico;

/* COFINS */
$cofins_aliquota = new \Imposto\COFINS\Aliquota();
$cofins_aliquota->setTributacao(\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
$cofins_aliquota->setBase(883.12);
$cofins_aliquota->setAliquota(7.60);
$impostos[] = $cofins_aliquota;

$cofins_quantidade = new \Imposto\COFINS\Quantidade();
$cofins_quantidade->setQuantidade(1000);
$cofins_quantidade->setAliquota(0.0076);
$impostos[] = $cofins_quantidade;

$cofins_isento = new \Imposto\COFINS\Isento();
$cofins_isento->setTributacao(\Imposto\COFINS\Isento::TRIBUTACAO_MONOFASICA);
$impostos[] = $cofins_isento;

$cofins_generico = new \Imposto\COFINS\Generico();
$cofins_generico->setValor(3.50);
$impostos[] = $cofins_generico;

/* IPI */
// Exemplo para Alíquota ad valorem
$ipi = new \Imposto\IPI();
$ipi_aliquota = new \Imposto\IPI\Aliquota();
$ipi_aliquota->setBase(1000.00);
$ipi_aliquota->setAliquota(7.00);
$ipi->setTributo($ipi_aliquota);
$impostos[] = $ipi;
// Exemplo para Alíquota específica
$ipi = new \Imposto\IPI();
$ipi_quantidade = new \Imposto\IPI\Quantidade();
$ipi_quantidade->setQuantidade(1000.00);
$ipi_quantidade->setPreco(0.7640);
$ipi->setTributo($ipi_quantidade);
$impostos[] = $ipi;
// Exemplo para operação Não Tributada (só utilize se for contribuinte do IPI)
$ipi = new \Imposto\IPI();
$ipi_isento = new \Imposto\IPI\Isento();
$ipi->setTributo($ipi_isento);
$impostos[] = $ipi;

/* II */
$ii = new \Imposto\II();
$ii->setBase(1000.00);
$ii->setDespesas(150.00);
$ii->setValor(100.00);
$ii->setIOF(80.00);
$impostos[] = $ii;

/* PISST */
$pisst_aliquota = new \Imposto\PISST\Aliquota();
$pisst_aliquota->setTributacao(\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
$pisst_aliquota->setBase(100.00);
$pisst_aliquota->setAliquota(0.65);
$impostos[] = $pisst_aliquota;

$pisst_quantidade = new \Imposto\PISST\Quantidade();
$pisst_quantidade->setQuantidade(1000);
$pisst_quantidade->setAliquota(0.0076);
$impostos[] = $pisst_quantidade;

/* COFINSST */
$cofins_aliquota = new \Imposto\COFINSST\Aliquota();
$cofins_aliquota->setTributacao(\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
$cofins_aliquota->setBase(100.00);
$cofins_aliquota->setAliquota(3.00);
$impostos[] = $cofins_aliquota;

$cofins_quantidade = new \Imposto\COFINSST\Quantidade();
$cofins_quantidade->setQuantidade(1000);
$cofins_quantidade->setAliquota(0.0076);
$impostos[] = $cofins_quantidade;

foreach ($impostos as $imposto) {
	$produto->addImposto($imposto);
}
$nfce->addProduto($produto);

/* Pagamentos */

$pagamento = new Pagamento();
$pagamento->setForma(Pagamento::FORMA_CREDITO);
$pagamento->setValor(4.50);
$pagamento->setCredenciadora('60889128000422');
$pagamento->setBandeira(Pagamento::BANDEIRA_MASTERCARD);
$pagamento->setAutorizacao('110011');
$nfce->addPagamento($pagamento);

$pagamento = new Pagamento();
$pagamento->setForma(Pagamento::FORMA_DINHEIRO);
$pagamento->setValor(4.00);
$nfce->addPagamento($pagamento);

$xml = $nfce->getNode();
$dom = $xml->ownerDocument;
$dom = $nfce->assinar($dom);
//$dom = $nfce->validar($dom); // valida o XML da nota

$dom->formatOutput = true;
$original_xml = $dom->saveXML();

$loaded_dom = new DOMDocument();
$loaded_dom->loadXML($original_xml);
$loaded_nfce = new NFCe();
$loaded_nfce->loadNode($loaded_dom);

$xml = $loaded_nfce->getNode();
$loaded_dom = $xml->ownerDocument;
$loaded_dom = $loaded_nfce->assinar($loaded_dom);
//$loaded_dom = $loaded_nfce->validar($loaded_dom); // valida o XML da nota
$loaded_dom->formatOutput = true;
$loaded_xml = $loaded_dom->saveXML();

if($original_xml != $loaded_xml) {
	header('Content-Type: application/xml');
	echo '<ERROR/>'."\n";
	echo $original_xml;
	echo $loaded_xml;
} else
	echo 'OK';