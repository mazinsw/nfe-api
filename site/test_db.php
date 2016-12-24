<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();
$banco = SEFAZ::getInstance()->getConfiguracao()->getBanco();
$data = $banco->getImpostoAliquota('22021000', 'PR');

header('Content-Type: application/json');
echo json_encode($data);