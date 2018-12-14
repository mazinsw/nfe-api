<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require __DIR__ . '/Gerador.php';
require __DIR__ . '/Processador.php';

use Example\Processador;
use NFe\Common\Certificado;

/* Preencha os campos abaixo para emitir uma NFC-e */

/* 1. Copie o certificado para o caminho /storage/certs/certificado.pfx */

/* Senha do certificado digital */
$senha_certificado = 'associacao';

/* Informações do contribuínte */
$contribuinte = [
    'csc' => 'INFORME O CSC AQUI',
    'token' => '000001', // altere caso seja diferente
];

/* Endereço do emitente */
$endereco = [
    'cep' => '',
    'cidade' => 'Rio de Janeiro',
    'estado' => 'Rio de Janeiro',
    'uf' => 'RJ',
    'bairro' => 'Centro',
    'logradouro' => '',
    'numero' => '',
];

/* Informações do emitente */
$emitente = [
    'razao_social' => 'RAZÃO SOCIAL DA EMPRESA',
    'fantasia' => 'NOME FANTASIA DA EMPRESA',
    'cnpj' => '', // CNPJ da empresa
    'ie' => '', // Inscrição estadual da empresa
    'endereco' => $endereco,
];

/* próximo numero a ser gerado a NFC-e */
$numero_da_nota = 1;

/* Não precisa alterar daqui pra baixo */

$cert_dir = dirname(dirname(__DIR__)) . '/storage/certs';
$certificado = new Certificado();
$certificado->setArquivoChavePublica($cert_dir . '/public.pem');
$certificado->setArquivoChavePrivada($cert_dir . '/private.pem');
$certificado->carrega($cert_dir . '/certificado.pfx', $senha_certificado, true);

$processador = new Processador();
$processador->init([
    'contrib' => $contribuinte,
    'emitente' => $emitente,
]);
$processador->getBanco()->nota_numero = $numero_da_nota;
$quantidade = $processador->processa();
echo "{$quantidade} notas processadas";
