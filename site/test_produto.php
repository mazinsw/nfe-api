<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$produto = new Produto();
$produto->setItem(1);
$produto->setCodigo(123456);
// $produto->setCodigoTributario($produto['codigo']);
$produto->setCodigoBarras('7894900011517');
$produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
$produto->setUnidade(ProdutoUnidade::UNIDADE);
// $produto->setMultiplicador($produto['multiplicador']);
// $produto->setPesoLiquido($produto['peso_liquido']);
// $produto->setPesoBruto($produto['peso_bruto']);
$produto->setPreco(4.50);
$produto->setQuantidade(2);
// $produto->setDesconto($produto['desconto']);
// $produto->setCFOP($produto['cfop']);
$produto->setNCM('2202.10.00');
$produto->setCEST('03.007.00');
// $produto->setImpostos($produto['impostos']);

$xml = $produto->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);