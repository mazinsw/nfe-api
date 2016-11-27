<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$cliente = new Cliente();
$cliente->setRazaoSocial('Empresa LTDA');
$cliente->setCNPJ('12345678000123');
$cliente->setEmail('contato@empresa.com.br');
$cliente->setIE('123456789');

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

$xml = $cliente->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);