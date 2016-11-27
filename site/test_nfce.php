<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$nfce = new NFCe();
$nfce->setCodigo('77882192');
$nfce->setSerie('1');
$nfce->setNumero('81');
$nfce->setDataEmissao(strtotime('2016-09-16T21:36:03-03:00'));
$nfce->setPresencaComprador(NFBasePresencaComprador::PRESENCIAL);

/* Emitente */
$emitente = new Emitente();
$emitente->setRazaoSocial('Empresa LTDA');
$emitente->setFantasia('Minha Empresa');
$emitente->setCNPJ('08380787000176');
$emitente->setTelefone('11955886644');
$emitente->setIE('123456789');
$emitente->setIM('95656');
$emitente->setRegime(EmitenteRegime::SIMPLES);

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->setUF('PR');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->setCodigo(4118402);
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$emitente->setEndereco($endereco);
$nfce->setEmitente($emitente);

/* Destinatário */
$cliente = new Cliente();
$cliente->setNome('Fulano da Silva');
$cliente->setCPF('12345678912');
$cliente->setEmail('fulano@site.com.br');
$cliente->setTelefone('11988220055');

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->setUF('PR');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->setCodigo(123456);
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$cliente->setEndereco($endereco);
$nfce->setCliente($cliente);

/* Produtos */
$produto = new Produto();
$produto->setItem(1);
$produto->setCodigo(123456);
$produto->setCodigoBarras('7894900011517');
$produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
$produto->setUnidade(ProdutoUnidade::UNIDADE);
$produto->setPreco(4.50);
$produto->setQuantidade(2);
$produto->setNCM('2202.10.00');
$produto->setCEST('03.007.00');
$nfce->addProduto($produto);

$produto = new Produto();
$produto->setItem(1);
$produto->setCodigo(123456);
$produto->setCodigoBarras('7894900011523');
$produto->setDescricao('REFRIGERANTE FANTA LARANJA 2L');
$produto->setUnidade(ProdutoUnidade::UNIDADE);
$produto->setPreco(4.00);
$produto->setQuantidade(1);
$produto->setNCM('2202.10.00');
$produto->setCEST('03.007.00');
$nfce->addProduto($produto);

/* Pagamentos */

$pagamento = new Pagamento();
$pagamento->setForma(PagamentoForma::CREDITO);
$pagamento->setValor(4.50);
$pagamento->setCredenciadora('60889128000422');
$pagamento->setBandeira(PagamentoBandeira::MASTERCARD);
$pagamento->setAutorizacao('110011');
$nfce->addPagamento($pagamento);

$pagamento = new Pagamento();
$pagamento->setForma(PagamentoForma::DINHEIRO);
$pagamento->setValor(4.00);
$nfce->addPagamento($pagamento);

$xml = $nfce->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML();