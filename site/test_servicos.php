<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();
$banco = SEFAZ::getInstance()->getConfiguracao()->getBanco();
$data = array(
	'PI' => $banco->getInformacaoServico(NF::EMISSAO_NORMAL, 'PI'),
	'PR' => $banco->getInformacaoServico(NF::EMISSAO_NORMAL, 'PR', 'nfce'),
	'PR' => $banco->getInformacaoServico(NF::EMISSAO_CONTINGENCIA, 'AC', 'nfe'),
);
header('Content-Type: application/json');
echo json_encode($data);
