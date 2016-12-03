<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();
$sefaz->getConfiguracao()->getEmitente()->getEndereco()
				 ->getMunicipio()->getEstado()->setUF('PR');

$produto = new Produto();
$produto->setItem(1);
$produto->setCodigo(123456);
// $produto->setCodigoTributario($produto['codigo']);
$produto->setCodigoBarras('7894900011517');
$produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
$produto->setUnidade(Produto::UNIDADE_UNIDADE);
// $produto->setMultiplicador($produto['multiplicador']);
// $produto->setPesoLiquido($produto['peso_liquido']);
// $produto->setPesoBruto($produto['peso_bruto']);
$produto->setPreco(4.50);
$produto->setQuantidade(2);
// $produto->setDesconto($produto['desconto']);
// $produto->setCFOP($produto['cfop']);
$produto->setNCM('22021000');
$produto->setCEST('0300700');
// $produto->setImpostos($produto['impostos']);

$xml = $produto->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);