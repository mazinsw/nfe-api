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
}
