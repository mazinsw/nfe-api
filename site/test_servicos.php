<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();
$banco = SEFAZ::getInstance()->getConfiguracao()->getBanco();
$data = $banco->getInformacaoServico('PI');

header('Content-Type: application/json');
echo json_encode($data);
