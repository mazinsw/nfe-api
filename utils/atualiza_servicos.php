<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
require_once(__DIR__ . '/../api/autoload.php');

use Curl\Curl;

function downloadServicesNFe($url)
{
	$curl = new Curl();
	$html = $curl->get($url);
	$data = array();
	$dom = new DOMDocument();
	$dom->loadHTML($html);
	$element = $dom->getElementById('notas');
	$links = $element->getElementsByTagName('a');
	$ufs = $element->getElementsByTagName('span');
	$serv = preg_replace('/[^A-Z]/', '', $links->item(0)->nodeValue);
	$urls = explode(', ', $ufs->item(0)->nodeValue);
	$redir = array(
		'servico' => $serv,
		'estados' => $urls
	);
	$data['outros'] = $redir;
	$serv = preg_replace('/[^A-Z]/', '', $links->item(1)->nodeValue);
	$cadastro = explode(', ', $ufs->item(1)->nodeValue);
	$demais = explode(', ', $ufs->item(2)->nodeValue);
	$redir = array(
		'servico' => $serv,
		'cadastro' => $cadastro,
		'demais' => $demais
	);
	$data['consulta'] = $redir;
	$serv = preg_replace('/[^A-Z]/', '', $links->item(2)->nodeValue);
	$urls = explode(', ', $ufs->item(3)->nodeValue);
	$redir1 = array(
		'servico' => $serv,
		'estados' => $urls
	);
	$serv = preg_replace('/[^A-Z]/', '', $links->item(3)->nodeValue);
	$urls = explode(', ', $ufs->item(4)->nodeValue);
	$redir2 = array(
		'servico' => $serv,
		'estados' => $urls
	);
	$data['contingencia'] = array($redir1, $redir2);
	$xpath = new DOMXpath($dom);
	$query = $xpath->query('//table[@class="tabelaListagemDados"]');
	$data['estados'] = array();
	foreach ($query as $node) {
		$caption = $node->getElementsByTagName('caption');
		$local = trim($caption->item(0)->nodeValue);
		$r = preg_match('/[^\(]+\(([^\)]+)\)/s', $local, $subject);
		$uf = $subject[1];
		$uf = preg_replace('/[^A-Z]/', '', $uf);
		$o = array();
		$o['uf'] = $uf;
		$rows = $node->getElementsByTagName('tr');
		$info = array();
		foreach ($rows as $index => $row) {
			if($index == 0)
				continue;
			$td = $row->getElementsByTagName('td');
			$servico = $td->item(0)->nodeValue;
			$versao_serv = $td->item(1)->nodeValue;
			$url_serv = trim($td->item(2)->nodeValue);
			$versoes = explode(' / ', $versao_serv);
			foreach ($versoes as $versao) {
				if(!isset($info[$versao]))
					$info[$versao] = array();
				$info[$versao][] = array(
					'servico' => $servico,
					'url' => $url_serv
				);
			}
		}
		$o['versoes'] = $info;
		$data['estados'][] = $o;
	}
	return $data;
}

function converteServicos($array)
{
	$service_map = array(
		'RecepcaoEvento' => 'evento',
		'NfeRecepcao' => 'recepcao',
		'NfeRetRecepcao' => 'confirmacao',
		'NfeDownloadNF' => 'download',
		'NfeConsultaDest' => 'destinadas',
		'NfeInutilizacao' => 'inutilizacao',
		'NfeConsultaProtocolo' => 'protocolo',
		'NfeStatusServico' => 'status',
		'NfeConsultaCadastro' => 'cadastro',
		'NFeAutorizacao' => 'autorizacao',
		'NFeRetAutorizacao' => 'retorno',
	);
	$data =  array();
	$normal = array();
	$contingencia = array();
	$nacional = array();
	foreach ($array as $ambiente => $info) {
		foreach ($info['estados'] as $estado) {
			if(in_array($estado['uf'], array('SVCAN', 'SVCRS')))
				$emissao = &$contingencia;
			else if(in_array($estado['uf'], array('AN')))
				$emissao = &$nacional;
			else
				$emissao = &$normal;
			if(!isset($emissao[$estado['uf']])) {
				$emissao[$estado['uf']] = array(
					'versao' => '3.10',
					'nfe' => array(),
					'nfce' => array(),
				);
			}
			$versao = $estado['versoes']['3.10'];
			if(is_null($versao))
				continue;
			$servicos = array();
			foreach ($versao as $servico) {
				$key = $service_map[$servico['servico']];
				$servicos[$key] = $servico['url'];
			}
			$emissao[$estado['uf']]['nfe'][$ambiente] = $servicos;
		}
		foreach ($info['outros']['estados'] as $estado) {
			$normal[$estado] = array(
				'versao' => '3.10',
				'nfe' => array('base' => $info['outros']['servico']),
				'nfce' => array(),
			);
		}
		foreach ($info['consulta']['demais'] as $estado) {
			$normal[$estado] = array(
				'versao' => '3.10',
				'nfe' => array('base' => $info['consulta']['servico']),
				'nfce' => array(),
			);
		}
		foreach ($info['contingencia'] as $row) {
			foreach ($row['estados'] as $estado) {
				$contingencia[$estado] = array(
					'versao' => '3.10',
					'nfe' => array('base' => $row['servico']),
					'nfce' => array(),
				);
			}
		}
	}
	$data['normal'] = $normal;
	$data['contingencia'] = $contingencia;
	$data['nacional'] = $nacional;
	return $data;
}

$dest_folder = $argv[1];

$url = array();
$url['producao'] = 'http://www.nfe.fazenda.gov.br/portal/WebServices.aspx';
$url['homologacao'] = 'http://hom.nfe.fazenda.gov.br/portal/webServices.aspx';
$data = array();
foreach ($url as $key => $value) {
	$data[$key] = downloadServicesNFe($value);
}

$data = converteServicos($data);

$data = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
$outfile = $dest_folder.'/servicos.json';
echo 'Writing '.$outfile."\n";
file_put_contents($outfile, $data);
