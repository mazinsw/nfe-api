<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../api/autoload.php');

$impostos = array();

/* PIS */
$pis_aliquota = new \Imposto\PIS\Aliquota();
$pis_aliquota->setTributacao(\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
$pis_aliquota->setBase(883.12);
$pis_aliquota->setAliquota(1.65);
$impostos[] = $pis_aliquota;

$pis_quantidade = new \Imposto\PIS\Quantidade();
$pis_quantidade->setQuantidade(1000);
$pis_quantidade->setAliquota(0.0076);
$impostos[] = $pis_quantidade;

$pis_isento = new \Imposto\PIS\Isento();
$pis_isento->setTributacao(\Imposto\PIS\Isento::TRIBUTACAO_MONOFASICA);
$impostos[] = $pis_isento;

$pis_generico = new \Imposto\PIS\Generico();
$pis_generico->setValor(3.50);
$impostos[] = $pis_generico;

/* COFINS */
$cofins_aliquota = new \Imposto\COFINS\Aliquota();
$cofins_aliquota->setTributacao(\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
$cofins_aliquota->setBase(883.12);
$cofins_aliquota->setAliquota(7.60);
$impostos[] = $cofins_aliquota;

$cofins_quantidade = new \Imposto\COFINS\Quantidade();
$cofins_quantidade->setQuantidade(1000);
$cofins_quantidade->setAliquota(0.0076);
$impostos[] = $cofins_quantidade;

$cofins_isento = new \Imposto\COFINS\Isento();
$cofins_isento->setTributacao(\Imposto\COFINS\Isento::TRIBUTACAO_MONOFASICA);
$impostos[] = $cofins_isento;

$cofins_generico = new \Imposto\COFINS\Generico();
$cofins_generico->setValor(3.50);
$impostos[] = $cofins_generico;

/* ICMS */
$icms_integral = new \Imposto\ICMS\Integral();
$icms_integral->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_integral->setBase(588.78);
$icms_integral->setAliquota(18.00);
$impostos[] = $icms_integral;

$icms_cobranca = new \Imposto\ICMS\Cobranca();
$icms_cobranca->getNormal()->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_cobranca->getNormal()->setBase(100.00);
$icms_cobranca->getNormal()->setAliquota(18.00);
$icms_cobranca->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_cobranca->setBase(135.00);
$icms_cobranca->setMargem(50.00);
$icms_cobranca->setReducao(10.00);
$icms_cobranca->setAliquota(18.00);
$impostos[] = $icms_cobranca;

$icms_reducao = new \Imposto\ICMS\Reducao();
$icms_reducao->setModalidade(\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
$icms_reducao->setBase(90.00);
$icms_reducao->setAliquota(18.0);
$icms_reducao->setReducao(10.0);
$impostos[] = $icms_reducao;

$icms_parcial = new \Imposto\ICMS\Parcial();
$icms_parcial->setModalidade(\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
$icms_parcial->setBase(135.00);
$icms_parcial->setMargem(50.00);
$icms_parcial->setReducao(10.00);
$icms_parcial->setAliquota(18.00);
$impostos[] = $icms_parcial;

$icms_isenta = new \Imposto\ICMS\Isenta();
$impostos[] = $icms_isenta;

$icms_isenta_cond = new \Imposto\ICMS\Isenta();
$icms_isenta_cond->setDesoneracao(1800.00);
$icms_isenta_cond->setMotivo(\Imposto\ICMS\Isenta::MOTIVO_TAXI);
$impostos[] = $icms_isenta_cond;

$icms_nao_trib = new \Imposto\ICMS\Isenta();
$icms_nao_trib->setTributacao('41');
$impostos[] = $icms_nao_trib;

$icms_suspensao = new \Imposto\ICMS\Isenta();
$icms_suspensao->setTributacao('50');
$impostos[] = $icms_suspensao;

// $icms_diferido = new \Imposto\ICMS\Diferido();
// $impostos[] = $icms_diferido;

$icms_cobrado = new \Imposto\ICMS\Cobrado();
$icms_cobrado->setBase(135.00);
$icms_cobrado->setValor(24.30);
$impostos[] = $icms_cobrado;

$dom = new DOMDocument('1.0', 'UTF-8');
$root = $dom->createElement('imposto');

foreach ($impostos as $imposto) {
	$node = $dom->importNode($imposto->getNode(), true);
	$root->appendChild($node);
}

$dom->appendChild($root);
$dom->formatOutput = true;
header('Content-Type: application/xml');
echo $dom->saveXML();