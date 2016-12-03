<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$volume = new Volume();
$volume->setQuantidade(2);
$volume->setEspecie('caixa');
$volume->setMarca('MZSW');
$volume->addNumeracao(1);
$volume->addNumeracao(2);
$volume->addNumeracao(3);
$volume->getPeso()
	->setLiquido(15.0)
	->setBruto(21.0);
$volume->addLacre(new Lacre(array('numero' => 123456)));
$volume->addLacre(new Lacre(array('numero' => 123457)));
$volume->addLacre(new Lacre(array('numero' => 123458)));

$xml = $volume->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);