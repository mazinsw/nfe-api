<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
require_once(__DIR__ . '/../vendor/autoload.php');

use NFe\Core\Nota;
use Curl\Curl;

function downloadServicesNFe($url)
{
    $curl = new Curl();
    $html = $curl->get($url);
    $data = [];
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $element = $dom->getElementById('notas');
    $links = $element->getElementsByTagName('a');
    $ufs = $element->getElementsByTagName('span');
    $serv = preg_replace('/[^A-Z]/', '', $links->item(0)->nodeValue);
    $urls = explode(', ', $ufs->item(0)->nodeValue);
    $redir = [
        'servico' => $serv,
        'estados' => $urls
    ];
    $data['outros'] = $redir;
    $serv = preg_replace('/[^A-Z]/', '', $links->item(1)->nodeValue);
    $cadastro = explode(', ', $ufs->item(1)->nodeValue);
    $demais = explode(', ', $ufs->item(2)->nodeValue);
    $redir = [
        'servico' => $serv,
        'cadastro' => $cadastro,
        'demais' => $demais
    ];
    $data['consulta'] = $redir;
    $serv = preg_replace('/[^A-Z]/', '', $links->item(2)->nodeValue);
    $urls = explode(', ', $ufs->item(3)->nodeValue);
    $redir1 = [
        'servico' => $serv,
        'estados' => $urls
    ];
    $serv = preg_replace('/[^A-Z]/', '', $links->item(3)->nodeValue);
    $urls = explode(', ', $ufs->item(4)->nodeValue);
    $redir2 = [
        'servico' => $serv,
        'estados' => $urls
    ];
    $data['contingencia'] = [$redir1, $redir2];
    $xpath = new DOMXpath($dom);
    $query = $xpath->query('//table[@class="tabelaListagemDados"]');
    $data['estados'] = [];
    foreach ($query as $node) {
        $caption = $node->getElementsByTagName('caption');
        $local = trim($caption->item(0)->nodeValue);
        $r = preg_match('/[^\(]+\(([^\)]+)\)/s', $local, $subject);
        $uf = $subject[1];
        $uf = preg_replace('/[^A-Z]/', '', $uf);
        $o = [];
        $o['uf'] = $uf;
        $rows = $node->getElementsByTagName('tr');
        $info = [];
        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue;
            }
            $td = $row->getElementsByTagName('td');
            $servico = $td->item(0)->nodeValue;
            $versao_serv = $td->item(1)->nodeValue;
            $url_serv = trim($td->item(2)->nodeValue);
            $versoes = explode(' / ', $versao_serv);
            foreach ($versoes as $versao) {
                if (!isset($info[$versao])) {
                    $info[$versao] = [];
                }
                $info[$versao][] = [
                    'servico' => $servico,
                    'url' => $url_serv
                ];
            }
        }
        $o['versoes'] = $info;
        $data['estados'][] = $o;
    }
    return $data;
}

function downloadServicesNFCe($url)
{
    $curl = new Curl();
    $html = $curl->get($url);
    $data = [];
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    $data['outros'] = ['estados' => []];
    $data['consulta'] = [
        'servico' => 'SVRS',
        'cadastro' => ['AC', 'RN', 'PB', 'SC'],
        'demais' => [
            'AC', 'AL', 'AP', 'BA', 'DF', 'ES', 'MA', 'PA', 'PB', 'PE', 'PI', 'RJ', 'RN', 'RO', 'RR', 'SC', 'SE', 'TO'
        ]
    ];
    $data['contingencia'] = [];
    $i = 0;
    $estados_uf = [
        'Amazonas' => 'AM',
        'Goiás' => 'GO',
        'Mato Grosso' => 'MT',
        'Mato Grosso do Sul' => 'MS',
        'Paraná' => 'PR',
        'Rio Grande do Sul' => 'RS',
        'SEFAZ Virtual-SVRS' => 'SVRS',
        'SEFAZ Virtual – SVRS' => 'SVRS',
        'São Paulo' => 'SP'

    ];
    $data['estados'] = [];
    $xpath = new DOMXpath($dom);
    $query = $xpath->query('//tbody');
    foreach ($query as $node) {
        $caption = $node->getElementsByTagName('td');
        $estado_index = $i == 0? 4: 0;
        $local = trim($caption->item($estado_index)->nodeValue);
        $amazonas = preg_replace('/[^A-Z]/i', '', $local);
        if ($amazonas == 'Amazonas') {
            $local = 'Amazonas';
        }
        $uf = $estados_uf[$local];
        $o = [];
        $o['uf'] = $uf;
        $rows = $node->getElementsByTagName('tr');
        $info = [];
        foreach ($rows as $index => $row) {
            if ($index == 0 || ($i == 0 && $index < 3)) {
                continue;
            }
            $td = $row->getElementsByTagName('td');
            $servico = $td->item(0)->nodeValue;
            $versao_serv = trim($td->item(1)->nodeValue);
            $url_serv = trim($td->item(2)->nodeValue);
            $versao_serv = preg_replace('/[\s]/i', '', $versao_serv);
            $versoes = empty($versao_serv) ? []: explode('/', $versao_serv);
            foreach ($versoes as $versao) {
                $versao = trim($versao);
                if (!isset($info[$versao])) {
                    $info[$versao] = [];
                }
                $info[$versao][] = [
                    'servico' => $servico,
                    'url' => $url_serv
                ];
            }
        }
        $o['versoes'] = $info;
        $data['estados'][] = $o;
        $i++;
    }
    return $data;
}

function converteServicos($nota, $nome)
{
    $service_map = [
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
        'NFeDistribuicaoDFe' => 'distribuicao',
        'CscNFCe' => 'administracao',
        'EPEC RecepcaoEvento' => 'contingencia',
        'EPEC Status Serviço' => 'servico'
    ];
    $data =  [];
    $normal = [];
    $contingencia = [];
    $nacional = [];
    foreach ($nota as $ambiente => $info) {
        foreach ($info['estados'] as $estado) {
            if (in_array($estado['uf'], ['SVCAN', 'SVCRS'])) {
                $emissao = &$contingencia;
            } elseif (in_array($estado['uf'], ['AN'])) {
                $emissao = &$nacional;
            } else {
                $emissao = &$normal;
            }
            if (!isset($emissao[$estado['uf']])) {
                $emissao[$estado['uf']] = [
                    'versao' => Nota::VERSAO,
                    $nome => [],
                ];
                if (!in_array($estado['uf'], ['SVCAN', 'SVCRS', 'AN'])) {
                    $emissao[$estado['uf']]['nfce'] = [];
                }
            }
            $versao = $estado['versoes'][Nota::VERSAO];
            if (is_null($versao)) {
                continue;
            }
            $servicos = [];
            foreach ($versao as $servico) {
                $key = $service_map[$servico['servico']];
                $servicos[$key] = [
                    'url' => $servico['url'],
                    'servico' => $servico['servico'],
                ];
            }
            /* adiciona serviços não existentes na versão atual */
            $versao = $estado['versoes']['3.10'];
            if (!is_null($versao)) {
                foreach ($versao as $servico) {
                    $key = $service_map[$servico['servico']];
                    if (isset($servicos[$key])) {
                        continue;
                    }
                    $servicos[$key] = [
                        'url' => $servico['url'],
                        'servico' => $servico['servico'],
                        'versao' => '3.10',
                    ];
                }
            }
            /* adiciona serviços não existentes na versão atual e 3.10 */
            $versao = $estado['versoes']['2.00'];
            if (!is_null($versao)) {
                foreach ($versao as $servico) {
                    $key = $service_map[$servico['servico']];
                    if (isset($servicos[$key])) {
                        continue;
                    }
                    $servicos[$key] = [
                        'url' => $servico['url'],
                        'servico' => $servico['servico'],
                        'versao' => '2.00',
                    ];
                }
            }
            /* adiciona serviços não existentes na versão atual, 3.10 e 2.00 */
            $versao = $estado['versoes']['1.00'];
            if (!is_null($versao)) {
                foreach ($versao as $servico) {
                    $key = $service_map[$servico['servico']];
                    if (isset($servicos[$key])) {
                        continue;
                    }
                    $servicos[$key] = [
                        'url' => $servico['url'],
                        'servico' => $servico['servico'],
                        'versao' => '1.00',
                    ];
                }
            }
            $emissao[$estado['uf']][$nome][$ambiente] = $servicos;
        }
        foreach ($info['outros']['estados'] as $estado) {
            $normal[$estado] = [
                'versao' => Nota::VERSAO,
                $nome => ['base' => $info['outros']['servico']]
            ];
        }
        foreach ($info['consulta']['demais'] as $estado) {
            $normal[$estado] = [
                'versao' => Nota::VERSAO,
                $nome => ['base' => $info['consulta']['servico']]
            ];
        }
        foreach ($info['contingencia'] as $row) {
            foreach ($row['estados'] as $estado) {
                $contingencia[$estado] = [
                    'versao' => Nota::VERSAO,
                    $nome => ['base' => $row['servico']],
                ];
            }
        }
    }
    $data['normal'] = $normal;
    $data['contingencia'] = $contingencia;
    $data['nacional'] = $nacional;
    return $data;
}

$dest_folder = $argv[1];

$url = [];
$url['producao'] = 'http://www.nfe.fazenda.gov.br/portal/WebServices.aspx';
$url['homologacao'] = 'http://hom.nfe.fazenda.gov.br/portal/webServices.aspx';
$nfe = [];
foreach ($url as $key => $value) {
    $nfe[$key] = downloadServicesNFe($value);
}
$nfe_data = converteServicos($nfe, 'nfe');
$url = array();
$url['producao'] = 'http://nfce.encat.org/desenvolvedor/webservices-p/';
$url['homologacao'] = 'http://nfce.encat.org/desenvolvedor/webservices-h/';
$nfce = array();
foreach ($url as $key => $value) {
    $nfce[$key] = downloadServicesNFCe($value);
}
$nfce_data = converteServicos($nfce, 'nfce');
$data = array_merge_recursive($nfe_data, $nfce_data);
$data = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
$outfile = $dest_folder.'/servicos.json';
echo 'Writing '.$outfile."\n";
file_put_contents($outfile, $data);
