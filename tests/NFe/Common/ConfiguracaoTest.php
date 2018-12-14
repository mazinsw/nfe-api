<?php
namespace NFe\Common;

class ConfiguracaoTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    protected function setUp()
    {
        $this->config = \NFe\Core\SEFAZ::getInstance(true)->getConfiguracao();
    }

    public function testObjetos()
    {
        $config = new Configuracao();
        $config->setToken('001');
        $config->setCSC('654af489d23b81484c8');
        $config->setTokenIBPT('a1a2a3');
        $new_config = new Configuracao();
        $new_config->fromArray($config);
        $new_config->fromArray($new_config->toArray());
        $new_config->fromArray(null);
        $this->assertNotNull($this->config);
        $this->config->fromArray($this->config);
        $this->config->fromArray($this->config->toArray());
        $this->config->fromArray(null);
        $banco = $this->config->getBanco();
        $this->assertNotNull($banco);
        $banco->fromArray($banco);
        $banco->fromArray($banco->toArray());
        $banco->fromArray(null);
        $emitente = $this->config->getEmitente();
        $this->assertNotNull($emitente);
    }

    public function testFields()
    {
        $this->config->setSincrono(false);
        $this->assertEquals('N', $this->config->getSincrono());
        $this->assertEquals('0', $this->config->getSincrono(true));
        $this->config->setSincrono(true);
        $this->assertEquals('Y', $this->config->getSincrono());
        $this->assertEquals('1', $this->config->getSincrono(true));
        $time = date('c');
        $this->config->setOffline($time);
        $this->assertEquals(Util::toDateTime(strtotime($time)), $this->config->getOffline(true));
        $this->config->setOffline(null);
    }

    public function testDataExpiracao()
    {
        $this->config
            ->setArquivoChavePublica(dirname(dirname(__DIR__)) . '/resources/certs/public.pem')
            ->setArquivoChavePrivada(dirname(dirname(__DIR__)) . '/resources/certs/private.pem');
        $this->config
            ->setChavePublica($this->config->getChavePublica())
            ->setChavePrivada($this->config->getChavePrivada());
        $this->assertEquals('2010-10-02', date('Y-m-d', $this->config->getExpiracao()));
    }
}
