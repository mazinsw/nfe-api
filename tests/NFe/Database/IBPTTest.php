<?php
namespace NFe\Database;

class IBPTTest extends \PHPUnit_Framework_TestCase
{
    private $ibpt;

    protected function setUp()
    {
        $this->ibpt = new \NFe\Database\IBPT();
        \NFe\Log\Logger::getInstance()->setWriteFunction(
            function ($type, $message) {
            }
        );
    }

    public function testAliquota()
    {
        $data = $this->ibpt->getImposto(null, null, '22021000', 'PR', 0);
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

        // TODO: Testar com dados de ambiente de testes
        $data = $this->ibpt->getImposto('00000000000000', '', '22021000', 'PR', 0);
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
        // offline
        $data = $this->ibpt->getImposto('00000000000000', '', '22021000', 'PR', 0);

        $data = $this->ibpt->getImposto(null, null, '22021000', 'ZZ', 0);
        $this->assertFalse($data);
    }
}
