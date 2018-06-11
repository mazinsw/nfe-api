<?php
namespace NFe\Task;

use NFe\Core\Nota;

class EnvioTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function createEnvio()
    {
        $dom = new \DOMDocument();
        $dom->loadXML('<a/>');
        $envio = new Envio();
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setModelo(Nota::MODELO_NFCE);
        $envio->setAmbiente(Nota::AMBIENTE_HOMOLOGACAO);
        $envio->setServico(Envio::SERVICO_INUTILIZACAO);
        $envio->setConteudo($dom);
        return $envio;
    }

    public function nulPostFunction($soap_curl, $url, $data)
    {
    }

    public function errorPostFunction($soap, $url, $data)
    {
        $soap->errorMessage = 'Not found';
        $soap->errorCode = '404';
        $soap->error = true;
    }

    public function testVersao()
    {
        $envio = new Envio();
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setModelo(Nota::MODELO_NFE);
        $envio->setAmbiente(Nota::AMBIENTE_HOMOLOGACAO);
        $envio->setServico(Envio::SERVICO_CONFIRMACAO);
        $this->assertEquals('2.00', $envio->getVersao());
    }

    public function testNormalization()
    {
        $envio = new Envio();
        $envio->setEmissao('1');
        $this->assertEquals(Nota::EMISSAO_NORMAL, $envio->getEmissao());
        $this->assertEquals('1', $envio->getEmissao(true));
        $envio->setEmissao('9');
        $this->assertEquals(Nota::EMISSAO_CONTINGENCIA, $envio->getEmissao());
        $this->assertEquals('9', $envio->getEmissao(true));
        $envio->setEmissao('2');
        $this->assertEquals('2', $envio->getEmissao(true));

        $envio->setModelo('65');
        $this->assertEquals(Nota::MODELO_NFCE, $envio->getModelo());
        $this->assertEquals('65', $envio->getModelo(true));
        $envio->setModelo('55');
        $this->assertEquals(Nota::MODELO_NFE, $envio->getModelo());
        $this->assertEquals('55', $envio->getModelo(true));
        $envio->setModelo('50');
        $this->assertEquals('50', $envio->getModelo(true));

        $envio->setAmbiente('1');
        $this->assertEquals(Nota::AMBIENTE_PRODUCAO, $envio->getAmbiente());
        $this->assertEquals('1', $envio->getAmbiente(true));
        $envio->setAmbiente('2');
        $this->assertEquals(Nota::AMBIENTE_HOMOLOGACAO, $envio->getAmbiente());
        $this->assertEquals('2', $envio->getAmbiente(true));
        $envio->setAmbiente('3');
        $this->assertEquals('3', $envio->getAmbiente(true));
    }

    public function testEnvioOffline()
    {
        $old_envio = self::createEnvio();
        $envio = new Envio($old_envio);
        $envio->fromArray($old_envio->toArray());
        $envio->fromArray(null);
        $this->sefaz->getConfiguracao()->setOffline(time());
        \NFe\Common\CurlSoap::setPostFunction([$this, 'nulPostFunction']);
        $this->setExpectedException('\NFe\Exception\NetworkException');
        try {
            $envio->envia();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            $this->sefaz->getConfiguracao()->setOffline(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->sefaz->getConfiguracao()->setOffline(null);
    }

    public function testEnvioErro()
    {
        $envio = self::createEnvio();
        \NFe\Common\CurlSoap::setPostFunction([$this, 'errorPostFunction']);
        $this->setExpectedException('\NFe\Exception\NetworkException');
        try {
            $envio->envia();
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            $this->sefaz->getConfiguracao()->setOffline(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->sefaz->getConfiguracao()->setOffline(null);
    }

    public function testEnvioAcaoInvalida()
    {
        $envio = self::createEnvio();
        $envio->setServico('qrcode');
        $this->setExpectedException('\Exception');
        $envio->getServico(true);
    }

    public function testEnvioServicoInvalido()
    {
        $envio = self::createEnvio();
        $envio->setServico('cancelar');
        $this->setExpectedException('\Exception');
        $envio->getServico(true);
    }
}
