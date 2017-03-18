<?php
namespace NFe\Task;

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
        $envio->setEmissao(\NFe\Core\Nota::EMISSAO_NORMAL);
        $envio->setModelo(\NFe\Core\Nota::MODELO_NFCE);
        $envio->setAmbiente(\NFe\Core\Nota::AMBIENTE_HOMOLOGACAO);
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

    public function testEnvioOffline()
    {
        $envio = self::createEnvio();
        $envio->fromArray($envio);
        $envio->fromArray(null);
        $this->sefaz->getConfiguracao()->setOffline(time());
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'nulPostFunction'));
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
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'errorPostFunction'));
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
