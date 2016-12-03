<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();
$db = SEFAZ::getInstance()->getConfiguracao()->getBanco();

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
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML();