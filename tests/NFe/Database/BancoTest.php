<?php

namespace NFe\Database;

use NFe\Core\Nota;
use NFe\Task\Envio;
use NFe\Core\NFCe;
use NFe\Common\Util;

class BancoTest extends \PHPUnit\Framework\TestCase
{
    private $banco;

    protected function setUp(): void
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

        $this->expectException('\Exception');
        $codigo = $this->banco->getCodigoMunicipio('Paranavaí', 'SP');
    }

    public function testCodigoMunicipioEstadoInvalido()
    {
        $this->expectException('\Exception');
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

        $this->expectException('\Exception');
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

        $this->expectException('\Exception');
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
        $this->expectException('\Exception');
        $data = $this->banco->getInformacaoServico('9', 'ZZ');
    }

    public function testInformacaoServicoEmissaoInvalida()
    {
        $this->expectException('\Exception');
        $data = $this->banco->getInformacaoServico('10', 'PI');
    }

    public function testInformacaoServicoModeloInvalido()
    {
        $this->expectException('\Exception');
        $data = $this->banco->getInformacaoServico(Nota::EMISSAO_NORMAL, 'PI', 'nfse');
    }

    public function testInformacaoServicoAmbienteInvalido()
    {
        $this->expectException('\Exception');
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

    /**
     * Renomear a função para testWSDL para verificar os endereços dos web services
     * Realizar essa operação regularmente para garantir, após a verificação,
     * voltar o nome da função para onlineTestWSDL
     */
    public function onlineTestWSDL()
    {
        global $app;

        $sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
        $sefaz->getConfiguracao()
            ->setArquivoChavePublica(dirname(dirname(dirname(__DIR__))) . '/docs/certs/public.pem')
            ->setArquivoChavePrivada(dirname(dirname(dirname(__DIR__))) . '/docs/certs/private.pem');
        $banco = $sefaz->getConfiguracao()->getBanco();
        $chave_publica = $sefaz->getConfiguracao()->getArquivoChavePublica();
        $chave_privada = $sefaz->getConfiguracao()->getArquivoChavePrivada();
        $soap = new \Curl\Curl();
        $soap->setOpt(CURLOPT_SSLCERT, $chave_publica);
        $soap->setOpt(CURLOPT_SSLKEY, $chave_privada);
        $ufs = ['AM', 'BA', 'CE', 'GO', 'MG', 'MS', 'MT', 'PE', 'PR', 'RS', 'SP', 'MA', 'PA', 'AC', 'AL', 'AP', 'DF',
            'ES', 'PB', 'PI', 'RJ', 'RN', 'RO', 'RR', 'SC', 'SE', 'TO'];
        $modelos = [Nota::MODELO_NFE, Nota::MODELO_NFCE];
        foreach ($modelos as $modelo) {
            foreach ($ufs as $uf) {
                $data = $banco->getInformacaoServico(
                    Nota::EMISSAO_NORMAL,
                    $uf,
                    $modelo
                );
                $ambientes = [Nota::AMBIENTE_PRODUCAO, Nota::AMBIENTE_HOMOLOGACAO];
                foreach ($ambientes as $ambiente) {
                    $servicos = $data[$ambiente];
                    foreach ($servicos as $servico => $values) {
                        if (!is_array($values)) {
                            continue;
                        }
                        $url = $values['url'] . '?wsdl';
                        $response = $soap->get($url);
                        if (!$soap->error) {
                            $namespace = (string)$response['targetNamespace'];
                            $action = str_replace(Nota::PORTAL . '/wsdl/', '', $namespace);
                            // echo $soap->rawResponse;
                            $this->assertEquals(
                                [$uf => [$modelo => [$ambiente => [$servico => ['servico' => $action]]]]],
                                [$uf => [$modelo => [$ambiente => [$servico => ['servico' => $values['servico']]]]]]
                            );
                        } else {
                            // exibe os erros de conexão, mas não interfere na execução dos testes
                            echo "\n" . 'ERROR(' . $url . ') = ' .
                                $soap->errorMessage . ': ' . $uf . ': ' . $servico . ': ' .
                                ', Ambiente: ' . $ambiente;
                        }
                    }
                }
            }
        }
    }
}
