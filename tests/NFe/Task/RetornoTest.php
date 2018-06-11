<?php
namespace NFe\Task;

class RetornoTest extends \PHPUnit_Framework_TestCase
{
    public static function createRetorno()
    {
        $retorno = new Retorno();
        $retorno->setDataRecebimento(time());
        return $retorno;
    }

    public function testRetornoCondicoes()
    {
        $retorno = self::createRetorno();
        $retorno->fromArray($retorno);
        $retorno->fromArray($retorno->toArray());
        $retorno->fromArray(null);
        $retorno->setStatus(101);
        $this->assertTrue($retorno->isCancelado());
        $retorno->setStatus(151);
        $this->assertTrue($retorno->isCancelado());
        $this->assertFalse($retorno->isDenegada());
        $this->assertFalse($retorno->isInexistente());
        $retorno->setStatus(110);
        $this->assertTrue($retorno->isDenegada());
        $retorno->setStatus(301);
        $this->assertTrue($retorno->isDenegada());
        $retorno->setStatus(302);
        $this->assertTrue($retorno->isDenegada());
        $retorno->setStatus(303);
        $this->assertTrue($retorno->isDenegada());
        $retorno->setStatus(217);
        $this->assertTrue($retorno->isInexistente());
    }
}
