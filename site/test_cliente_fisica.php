<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');
$sefaz = SEFAZ::init();

$cliente = new Cliente();
$cliente->setNome('Fulano da Silva');
$cliente->setCPF('12345678912');
$cliente->setEmail('fulano@site.com.br');
$cliente->setTelefone('11988220055');

$endereco = new Endereco();
$endereco->setCEP('01122500');
$endereco->getMunicipio()
		 ->setNome('Paranavaí')
		 ->setCodigo(123456)
		 ->getEstado()
		 ->setUF('PR');
$endereco->setBairro('Centro');
$endereco->setLogradouro('Rua Paranavaí');
$endereco->setNumero('123');

$cliente->setEndereco($endereco);

$xml = $cliente->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);