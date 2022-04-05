<?php

namespace NFe\Common;

class CertificadoTest extends \PHPUnit\Framework\TestCase
{
    public function testCarrega()
    {
        $root = dirname(dirname(dirname(__DIR__)));
        $certificado = new Certificado();
        $certificado->setArquivoChavePublica($root . '/storage/certs/public.pem');
        $certificado->setArquivoChavePrivada($root . '/storage/certs/private.pem');
        $certificado->carrega($root . '/docs/certs/certificado.pfx', 'associacao', true);
        $this->assertEquals('2010-10-02', date('Y-m-d', $certificado->getExpiracao()));
    }

    public function testCarregaFalhaAbrir()
    {
        $certificado = new Certificado();
        $this->expectException('\Exception');
        $certificado->carrega('invalido.pfx', 'invalido');
    }

    public function testCarregaFalhaLeitura()
    {
        $root = dirname(dirname(dirname(__DIR__)));
        $certificado = new Certificado();
        $this->expectException('\Exception');
        $certificado->carrega($root . '/docs/certs/certificado.pfx', 'invalido');
    }

    public function testDataExpiracao()
    {
        $certificado = new Certificado(['chave_publica' => '0', 'chave_privada' => '0']);
        $certificado->setArquivoChavePublica(dirname(dirname(__DIR__)) . '/resources/certs/public.pem');
        $certificado->setArquivoChavePrivada(dirname(dirname(__DIR__)) . '/resources/certs/private.pem');
        $certificado->fromArray($certificado);
        $certificado->fromArray(null);
        $this->assertEquals('2010-10-02', date('Y-m-d', $certificado->getExpiracao()));
        $this->expectException('\Exception');
        $certificado->requerValido();
    }

    public function testNode()
    {
        $certificado = new Certificado();
        try {
            $this->assertNull($certificado->getExpiracao());
            $certificado->requerValido();
            $this->fail('Exceção não lançada na função requerValido');
        } catch (\Exception $e) {
        }
        try {
            $certificado->getNode();
            $this->fail('Exceção não lançada na função getNode');
        } catch (\Exception $e) {
        }
        $this->expectException('\Exception');
        $certificado->loadNode(null);
    }
}
