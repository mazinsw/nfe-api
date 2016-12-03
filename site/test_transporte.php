<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$transporte = new Transporte();
$transporte->setFrete(Transporte::FRETE_EMITENTE);
$transporte->getVeiculo()
		   ->setRNTC(123456789)
		   ->setPlaca('ALK1232')
		   ->setUF('PR');
$transporte->getReboque()
		   ->setPlaca('KLM1234')
		   ->setUF('PI');

$transportador = new \Transporte\Transportador();
$transportador->setRazaoSocial('Empresa LTDA');
$transportador->setCNPJ('12345678000123');
$transportador->setIE('123456789');

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

$transportador->setEndereco($endereco);
$transporte->setTransportador($transportador);

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

$transporte->setVolume($volume);

$xml = $transporte->getNode();
$dom = $xml->ownerDocument;
$dom->formatOutput = true;

header('Content-Type: application/xml');
echo $dom->saveXML($xml);