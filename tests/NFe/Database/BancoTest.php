<?php
namespace NFe\Database;

use NFe\Core\Nota;
use NFe\Task\Envio;
use NFe\Core\NFCe;
use NFe\Common\Util;

class BancoTest extends \PHPUnit_Framework_TestCase
{
    private $banco;

    protected function setUp()
    {
        $sefaz = \NFe\Core\SEFAZ::getInstance(true);
        $this->banco = $sefaz->getConfiguracao()->getBanco();
    }

    public function testAliquota()
    {
        $this->banco->fromArray($this->banco);
        $this->banco->fromArray($this->banco->toArray());
        $this->banco->fromArray(null);
        $data = $this->banco->getImpostoAliquota('22021000', 'PR');
        $this->assertArrayHasKey('importado', $data);
        $this->assertArrayHasKey('nacional', $data);
        $this->assertArrayHasKey('estadual', $data);
        $this->assertArrayHasKey('municipal', $data);
        $this->assertArrayHasKey('tipo', $data);
        $this->assertArrayHasKey('info', $data);
        $this->assertArrayHasKey('fonte', $data['info']);
        $this->assertArrayHasKey('versao', $data['info']);
        $this->assertArrayHasKey('chave', $data['info']);
        $this->assertArrayHasKey('vigencia', $data['info']);
        $this->assertArrayHasKey('inicio', $data['info']['vigencia']);
        $this->assertArrayHasKey('fim', $data['info']['vigencia']);
        $this->assertArrayHasKey('origem', $data['info']);

        $data = $this->banco->getImpostoAliquota('22021000', 'ZZ');
        $this->assertFalse($data);
    }

    public function testCodigoMunicipio()
    {
        $codigo = $this->banco->getCodigoMunicipio('Teresina', 'PI');
        $this->assertEquals($codigo, 2211001);

        $codigo = $this->banco->getCodigoMunicipio('São Paulo', 'SP');
        $this->assertEquals($codigo, 3550308);

        $codigo = $this->banco->getCodigoMunicipio('Paranavaí', 'PR');
        $this->assertEquals($codigo, 4118402);

        $this->setExpectedException('\Exception');
        $codigo = $this->banco->getCodigoMunicipio('Paranavaí', 'SP');
    }

    public function testCodigoMunicipioEstadoInvalido()
    {
        $this->setExpectedException('\Exception');
        $codigo = $this->banco->getCodigoMunicipio('Inválido', 'ZZ');
    }

    public function testCodigoEstado()
    {
        $codigo = $this->banco->getCodigoEstado('PR');
        $this->assertEquals($codigo, 41);

        $codigo = $this->banco->getCodigoEstado('SP');
        $this->assertEquals($codigo, 35);

        $codigo = $this->banco->getCodigoEstado('PI');
        $this->assertEquals($codigo, 22);

        $this->setExpectedException('\Exception');
        $codigo = $this->banco->getCodigoEstado('ZZ');
    }

    public function testCodigoOrgao()
    {
        $codigo = $this->banco->getCodigoOrgao('PR');
        $this->assertEquals($codigo, 41);

        $codigo = $this->banco->getCodigoOrgao('SP');
        $this->assertEquals($codigo, 35);

        $codigo = $this->banco->getCodigoOrgao('AN');
        $this->assertEquals($codigo, 91);

        $this->setExpectedException('\Exception');
        $codigo = $this->banco->getCodigoOrgao('ZZ');
    }


    public function testInformacaoServico()
    {
        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_NORMAL, 'PI');
        $this->assertArrayHasKey('nfe', $data);
        $this->assertArrayHasKey('homologacao', $data['nfe']);
        $this->assertArrayHasKey('inutilizacao', $data['nfe']['homologacao']);
        $this->assertArrayHasKey('protocolo', $data['nfe']['homologacao']);
        $this->assertArrayHasKey('status', $data['nfe']['homologacao']);
        $this->assertArrayHasKey('autorizacao', $data['nfe']['homologacao']);
        $this->assertArrayHasKey('retorno', $data['nfe']['homologacao']);
        $this->assertArrayHasKey('evento', $data['nfe']['homologacao']);

        $this->assertArrayHasKey('producao', $data['nfe']);
        $this->assertArrayHasKey('inutilizacao', $data['nfe']['producao']);
        $this->assertArrayHasKey('protocolo', $data['nfe']['producao']);
        $this->assertArrayHasKey('status', $data['nfe']['producao']);
        $this->assertArrayHasKey('autorizacao', $data['nfe']['producao']);
        $this->assertArrayHasKey('retorno', $data['nfe']['producao']);
        $this->assertArrayHasKey('evento', $data['nfe']['producao']);

        $this->assertArrayHasKey('nfce', $data);
        $this->assertArrayHasKey('homologacao', $data['nfce']);
        $this->assertArrayHasKey('qrcode', $data['nfce']['homologacao']);
        $this->assertArrayHasKey('inutilizacao', $data['nfce']['homologacao']);
        $this->assertArrayHasKey('protocolo', $data['nfce']['homologacao']);
        $this->assertArrayHasKey('status', $data['nfce']['homologacao']);
        $this->assertArrayHasKey('autorizacao', $data['nfce']['homologacao']);
        $this->assertArrayHasKey('retorno', $data['nfce']['homologacao']);
        $this->assertArrayHasKey('evento', $data['nfce']['homologacao']);

        $this->assertArrayHasKey('producao', $data['nfce']);
        $this->assertArrayHasKey('qrcode', $data['nfce']['producao']);
        $this->assertArrayHasKey('inutilizacao', $data['nfce']['producao']);
        $this->assertArrayHasKey('protocolo', $data['nfce']['producao']);
        $this->assertArrayHasKey('status', $data['nfce']['producao']);
        $this->assertArrayHasKey('autorizacao', $data['nfce']['producao']);
        $this->assertArrayHasKey('retorno', $data['nfce']['producao']);
        $this->assertArrayHasKey('evento', $data['nfce']['producao']);

        $data = $this->banco->getInformacaoServico('1', 'PR', 'nfce');
        $data = $this->banco->getInformacaoServico('1', 'PR', '65');
        $this->assertArrayHasKey('homologacao', $data);
        $this->assertArrayHasKey('qrcode', $data['homologacao']);
        $this->assertArrayHasKey('inutilizacao', $data['homologacao']);
        $this->assertArrayHasKey('protocolo', $data['homologacao']);
        $this->assertArrayHasKey('status', $data['homologacao']);
        $this->assertArrayHasKey('autorizacao', $data['homologacao']);
        $this->assertArrayHasKey('retorno', $data['homologacao']);
        $this->assertArrayHasKey('evento', $data['homologacao']);

        $this->assertArrayHasKey('producao', $data);
        $this->assertArrayHasKey('qrcode', $data['producao']);
        $this->assertArrayHasKey('inutilizacao', $data['producao']);
        $this->assertArrayHasKey('protocolo', $data['producao']);
        $this->assertArrayHasKey('status', $data['producao']);
        $this->assertArrayHasKey('autorizacao', $data['producao']);
        $this->assertArrayHasKey('retorno', $data['producao']);
        $this->assertArrayHasKey('evento', $data['producao']);

        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_CONTINGENCIA, 'AC', 'nfe');
        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_CONTINGENCIA, 'AC', '55');
        $this->assertArrayHasKey('homologacao', $data);
        $this->assertArrayHasKey('protocolo', $data['homologacao']);
        $this->assertArrayHasKey('status', $data['homologacao']);
        $this->assertArrayHasKey('autorizacao', $data['homologacao']);
        $this->assertArrayHasKey('retorno', $data['homologacao']);
        $this->assertArrayHasKey('evento', $data['homologacao']);

        $this->assertArrayHasKey('producao', $data);
        $this->assertArrayHasKey('protocolo', $data['producao']);
        $this->assertArrayHasKey('status', $data['producao']);
        $this->assertArrayHasKey('autorizacao', $data['producao']);
        $this->assertArrayHasKey('retorno', $data['producao']);
        $this->assertArrayHasKey('evento', $data['producao']);

        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_NORMAL, 'AC', 'nfe', '1');
        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_NORMAL, 'AC', 'nfe', '2');
        $this->assertArrayHasKey('inutilizacao', $data);
        $this->assertArrayHasKey('protocolo', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('autorizacao', $data);
        $this->assertArrayHasKey('retorno', $data);
        $this->assertArrayHasKey('evento', $data);

        // estado inválido
        $this->setExpectedException('\Exception');
        $data = $this->banco->getInformacaoServico('9', 'ZZ');
    }

    public function testInformacaoServicoEmissaoInvalida()
    {
        $this->setExpectedException('\Exception');
        $data = $this->banco->getInformacaoServico('10', 'PI');
    }

    public function testInformacaoServicoModeloInvalido()
    {
        $this->setExpectedException('\Exception');
        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_NORMAL, 'PI', 'nfse');
    }

    public function testInformacaoServicoAmbienteInvalido()
    {
        $this->setExpectedException('\Exception');
        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_NORMAL, 'PI', 'nfe', 'teste');
    }

    public function testNotasTarefas()
    {
        $notas = $this->banco->getNotasAbertas(0, 0);
        $this->assertCount(0, $notas);

        $notas = $this->banco->getNotasPendentes(0, 0);
        $this->assertCount(0, $notas);

        $tarefas = $this->banco->getNotasTarefas(0, 0);
        $this->assertCount(0, $tarefas);
    }

    public function testNFCeServices()
    {
        $filename = dirname(dirname(__DIR__)) . '/resources/wsnfe_4.00_mod65.xml';
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->load($filename);
        $ufs = $dom->getElementsByTagName('UF');
        $servicos = [
            'NfeAutorizacao' => 'autorizacao',
            'NfeRetAutorizacao' => 'retorno',
            'NfeInutilizacao' => 'inutilizacao',
            'NfeConsultaProtocolo' => 'protocolo',
            'NfeStatusServico' => 'status',
            'RecepcaoEvento' => 'evento',
            'NfeConsultaQR' => 'qrcode',
            'CscNFCe' => 'administracao'
        ];
        $ambientes = [Nota::AMBIENTE_PRODUCAO, Nota::AMBIENTE_HOMOLOGACAO];
        $normal = [];
        foreach ($ufs as $uf_node) {
            $sigla = Util::loadNode($uf_node, 'sigla');
            foreach ($ambientes as $ambiente) {
                $node_ambiente = Util::findNode($uf_node, $ambiente);
                foreach ($node_ambiente->childNodes as $servico_node) {
                    if (!isset($servicos[$servico_node->nodeName])) {
                        continue;
                    }
                    $name = $servicos[$servico_node->nodeName];
                    $url = trim($servico_node->nodeValue);
                    if ($url == '') {
                        continue;
                    }
                    $servico = $servico_node->getAttribute('operation');
                    $version = $servico_node->getAttribute('version');
                    if ($name == 'qrcode') {
                        $normal[$sigla]['nfce'][$ambiente][$name] = trim($url, '?');
                    } else {
                        $normal[$sigla]['nfce'][$ambiente][$name]['url'] = $url;
                        $normal[$sigla]['nfce'][$ambiente][$name]['servico'] = $servico;
                        if ($version != Nota::VERSAO) {
                            $normal[$sigla]['nfce'][$ambiente][$name]['versao'] = $version;
                        }
                    }
                }
            }
        }
        $filename = dirname(dirname(__DIR__)) . '/resources/uri_consulta_nfce.json';
        $json = file_get_contents($filename);
        $consultas = json_decode($json, true);
        $ambientes_codes = [Nota::AMBIENTE_PRODUCAO => 1, Nota::AMBIENTE_HOMOLOGACAO => 2];
        foreach ($ambientes_codes as $ambiente => $ambiente_code) {
            foreach ($consultas[$ambiente_code] as $sigla => $url) {
                if ($url == '') {
                    continue;
                }
                $normal[$sigla]['nfce'][$ambiente]['consulta'] = trim($url, '?&');
            }
        }
        // correções
        $normal['MT']['nfce'][Nota::AMBIENTE_PRODUCAO]['protocolo']['servico'] = 'NFeConsultaProtocolo4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['autorizacao']['url'] = 'https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeAutorizacao4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['retorno']['url'] = 'https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeRetAutorizacao4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['inutilizacao']['url'] = 'https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeInutilizacao4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['protocolo']['url'] = 'https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeConsulta4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['protocolo']['servico'] = 'NFeConsultaProtocolo4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['status']['url'] = 'https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeStatusServico4';
        $normal['MT']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['evento']['url'] = 'https://homologacao.sefaz.mt.gov.br/nfcews/services/RecepcaoEvento4';

        $normal['BA']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['consulta'] = 'http://hnfe.sefaz.ba.gov.br/servicos/nfce/default.aspx';
        $normal['GO']['nfce'][Nota::AMBIENTE_PRODUCAO]['consulta'] = 'http://www.nfce.go.gov.br/post/ver/214344/consulta-nfce';
        $normal['GO']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['consulta'] = 'http://www.nfce.go.gov.br/post/ver/214413/consulta-nfc-e-homologacao';
        $normal['PE']['nfce'][Nota::AMBIENTE_PRODUCAO]['consulta'] = 'http://nfce.sefaz.pe.gov.br/nfce-web/consultarNFCe';
        $normal['PE']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['consulta'] = 'http://nfcehomolog.sefaz.pe.gov.br/nfce-web/consultarNFCe';
        $normal['RN']['nfce'][Nota::AMBIENTE_PRODUCAO]['consulta'] = 'http://nfce.set.rn.gov.br/portalDFE/NFCe/ConsultaNFCe.aspx';
        $normal['RN']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['consulta'] = 'http://hom.nfce.set.rn.gov.br/portalDFE/NFCe/ConsultaNFCe.aspx';
        $normal['RO']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['consulta'] = 'http://www.nfce.sefin.ro.gov.br/consultaAmbHomologacao.jsp';
        $normal['SP']['nfce'][Nota::AMBIENTE_PRODUCAO]['consulta'] = 'https://www.nfce.fazenda.sp.gov.br/consulta';
        $normal['SP']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['consulta'] = 'https://www.homologacao.nfce.fazenda.sp.gov.br/consulta';

        $normal['PR']['nfce'][Nota::AMBIENTE_PRODUCAO]['qrcode'] = 'http://www.fazenda.pr.gov.br/nfce/qrcode/';
        $normal['PR']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['qrcode'] = 'http://www.fazenda.pr.gov.br/nfce/qrcode/';
        $normal['SE']['nfce'][Nota::AMBIENTE_PRODUCAO]['qrcode'] = 'http://www.nfce.se.gov.br/portal/consultarNFCe.jsp';
        $normal['TO']['nfce'][Nota::AMBIENTE_HOMOLOGACAO]['qrcode'] = 'http://apps.sefaz.to.gov.br/portal-nfce-homologacao/qrcodeNFCe';
        foreach ($normal as $uf => $values) {
            $data_uf = $this->banco->getInformacaoServico(
                Nota::EMISSAO_NORMAL,
                $uf,
                Nota::MODELO_NFCE
            );
            foreach ($ambientes as $ambiente) {
                if (isset($data_uf['versao'])) {
                    $default_versao = $data_uf['versao'];
                } else {
                    $default_versao = Nota::VERSAO;
                }
                $servicos = $values['nfce'][$ambiente];
                $urls = ['qrcode', 'consulta'];
                $data = $data_uf[$ambiente];
                foreach ($servicos as $servico => $url) {
                    if (!isset($data[$servico])) {
                        $this->fail(
                            sprintf(
                                'Não existe url de %s para o serviço "%s", versão "%s" e estado "%s"',
                                $ambiente,
                                $servico,
                                $default_versao,
                                $uf
                            )
                        );
                    }
                    $info = $data[$servico];
                    $this->assertEquals(
                        [$uf => [$ambiente => [$servico => $url]]],
                        [$uf => [$ambiente => [$servico => $info]]]
                    );
                }
            }
        }
    }
}
