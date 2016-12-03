<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$sefaz = SEFAZ::init();
$banco = SEFAZ::getInstance()->getConfiguracao()->getBanco();
$aliq = $banco->getImpostoAliquota('22021000', 'PR');
if(!Util::isEqual($aliq[Imposto::TIPO_NACIONAL], 15.41))
	die('Error: getImpostoAliquota(22021000, PR) != '.$aliq);
$cod = $banco->getCodigoEstado('PR');
if($cod != 41)
	die('Error: getCodigoEstado(PR) != '.$cod);
echo 'ok';