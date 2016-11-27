<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$pagamento = new Pagamento();
$pagamento->setForma(PagamentoForma::CREDITO);
$pagamento->setValor(4.50);
$pagamento->setCredenciadora('60889128000422');
$pagamento->setBandeira(PagamentoBandeira::MASTERCARD);
$pagamento->setAutorizacao('110011');

$xml = $pagamento->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);