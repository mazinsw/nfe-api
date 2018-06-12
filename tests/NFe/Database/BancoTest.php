<?php
namespace NFe\Database;

use NFe\Core\Nota;
use NFe\Task\Envio;
use NFe\Core\NFCe;

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

    public function testServicesACBR()
    {
        $ini = dirname(dirname(__DIR__)) . '/resources/ACBrNFeServicos.ini';
        $content = \file_get_contents($ini);
        // $content = \preg_replace('/[;]+[^\n\r]*[\n\r]*/', '', $content);
        $values = \parse_ini_string($content, true, INI_SCANNER_RAW);
        settype($values, 'array');
        $estados = [
            'AC',
            'AL',
            'AP',
            'AM',
            'BA',
            'CE',
            'DF',
            'ES',
            'GO',
            'MA',
            'MT',
            'MS',
            'MG',
            'PA',
            'PB',
            'PR',
            'PE',
            'PI',
            'RJ',
            'RN',
            'RS',
            'RO',
            'RR',
            'SC',
            'SP',
            'SE',
            'TO'
        ];
        $info_servicos = [
            'qrcode' => 'QRCode',
            'consulta' => 'ConsultaNFCe'
        ];
        $info_ambientes = [
            Nota::AMBIENTE_PRODUCAO => 'P',
            Nota::AMBIENTE_HOMOLOGACAO => 'H'
        ];
        $ambientes = [Nota::AMBIENTE_PRODUCAO, Nota::AMBIENTE_HOMOLOGACAO];
        foreach ($estados as $uf) {
            foreach ($ambientes as $ambiente) {
                $data = $this->banco->getInformacaoServico(
                    Nota::EMISSAO_NORMAL,
                    $uf,
                    Nota::MODELO_NFCE
                );
                if (isset($data['versao'])) {
                    $default_versao = $data['versao'];
                } else {
                    $default_versao = Nota::VERSAO;
                }
                $data = $data[$ambiente];
                $section = sprintf('NFCe_%s_%s', $uf, $info_ambientes[$ambiente]);
                $entries = $values[$section];
                if (isset($entries['Usar'])) {
                    $other = $values[$entries['Usar']];
                    $entries = array_merge($entries, $other);
                }
                $servicos = [
                    Envio::SERVICO_INUTILIZACAO,
                    Envio::SERVICO_PROTOCOLO,
                    Envio::SERVICO_STATUS,
                    Envio::SERVICO_CADASTRO,
                    Envio::SERVICO_AUTORIZACAO,
                    Envio::SERVICO_RETORNO,
                    Envio::SERVICO_RECEPCAO,
                    Envio::SERVICO_CONFIRMACAO,
                    Envio::SERVICO_EVENTO
                ];
                foreach ($servicos as $servico) {
                    if (!isset($data[$servico])) {
                        continue;
                    }
                    $info = $data[$servico];
                    if (is_array($info) && isset($info['versao'])) {
                        $versao = $info['versao'];
                    } else {
                        $versao = $default_versao;
                    }
                    $entry = sprintf('%s_%s', $info['servico'], $versao);
                    if (!isset($entries[$entry])) {
                        $this->fail(
                            sprintf(
                                'Não existe url de %s para o serviço "%s", versão "%s" e estado "%s"',
                                $ambiente,
                                $info['servico'],
                                $versao,
                                $uf
                            )
                        );
                    }
                    $url = trim($entries[$entry]);
                    $this->assertEquals(
                        [$uf => [$ambiente => [$info['servico'] => $url]]],
                        [$uf => [$ambiente => [$info['servico'] => $info['url']]]]
                    );
                }
                $urls = ['qrcode', 'consulta'];
                foreach ($urls as $servico) {
                    if (!isset($data[$servico])) {
                        continue;
                    }
                    $info = $data[$servico];
                    if (is_array($info) && isset($info['versao'])) {
                        $versao = $info['versao'];
                    } else {
                        $versao = NFCe::QRCODE_VERSAO;
                        $versao = preg_replace('/(\d)(\d{2})/', '$1.$2', NFCe::QRCODE_VERSAO);
                    }
                    if (is_array($info)) {
                        $info_url = $info['url'];
                    } else {
                        $info_url = $info;
                    }
                    $info_servico = $info_servicos[$servico];
                    $entry = sprintf('URL-%s_%s', $info_servico, $versao);
                    if (!isset($entries[$entry]) && $versao = '1.00') {
                        $entry = sprintf('URL-%s', $info_servico);
                    }
                    if (!isset($entries[$entry])) {
                        $this->fail(
                            sprintf(
                                'Não existe url para o serviço "%s", versão "%s" e estado "%s"',
                                $info_servico,
                                $versao,
                                $uf
                            )
                        );
                    }
                    $url = trim($entries[$entry]);
                    $this->assertEquals(
                        [$uf => [$ambiente => [$info_servico => $url]]],
                        [$uf => [$ambiente => [$info_servico => $info_url]]]
                    );
                }
            }
        }
    }
}
