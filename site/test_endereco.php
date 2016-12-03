<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');
$sefaz = SEFAZ::init();

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->getEstado()
		 ->setUF('PR');
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');
$endereco->setComplemento('Loteamento Paranavaí');

$xml = $endereco->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);