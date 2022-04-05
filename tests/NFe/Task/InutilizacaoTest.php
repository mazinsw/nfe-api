<?php

namespace NFe\Task;

use NFe\Core\Nota;

class InutilizacaoTest extends \PHPUnit\Framework\TestCase
{
    public static function criaInutilizacao()
    {
        \NFe\Core\SEFAZ::getInstance()
            ->getConfiguracao()->getEmitente()->getEndereco()
            ->getMunicipio()->getEstado()->setUF('PR');
        \NFe\Core\SEFAZ::getInstance()
            ->getConfiguracao()->getEmitente()->setCNPJ('08380787000176');
        $inutilizacao = new \NFe\Task\Inutilizacao();
        $inutilizacao->setUF(\NFe\Core\SEFAZ::getInstance()
            ->getConfiguracao()->getEmitente()->getEndereco()
            ->getMunicipio()->getEstado()->getUF());
        $inutilizacao->setCNPJ(\NFe\Core\SEFAZ::getInstance()
            ->getConfiguracao()->getEmitente()->getCNPJ());
        $inutilizacao->setAmbiente(\NFe\Core\Nota::AMBIENTE_HOMOLOGACAO);
        $inutilizacao->setAno(2017);
        $inutilizacao->setModelo(65);
        $inutilizacao->setSerie(1);
        $inutilizacao->setInicio(81);
        $inutilizacao->setFinal($inutilizacao->getInicio());
        $inutilizacao->setJustificativa('TESTE DO SISTEMA');
        return $inutilizacao;
    }

    public function inutilizadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testInutilizaSOAP.xml',
            'task/testInutilizaInutilizadoReponseSOAP.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        \NFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testInutilizaSOAP.xml',
            'task/testInutilizaRejeicaoReponseSOAP.xml'
        );
    }

    public function testInutilizaInutilizado()
    {
        \NFe\Common\CurlSoap::setPostFunction([$this, 'inutilizadoPostFunction']);
        try {
            $inutilizacao = self::criaInutilizacao();
            $dom = $inutilizacao->getNode()->ownerDocument;
            $dom = $inutilizacao->assinar($dom);
            $dom = $inutilizacao->envia($dom);
            $inutilizacao->fromArray($inutilizacao);
            $inutilizacao->fromArray($inutilizacao->toArray());
            $inutilizacao->fromArray(null);
        } catch (\Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertEquals('102', $inutilizacao->getStatus());
        $this->assertEquals('141170000156683', $inutilizacao->getNumero());
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testInutilizaRejeitado()
    {
        \NFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        $inutilizacao = self::criaInutilizacao();
        $dom = $inutilizacao->getNode()->ownerDocument;
        $dom = $inutilizacao->assinar();
        $this->expectException('\Exception');
        try {
            $inutilizacao->envia($dom);
        } catch (\Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            $this->assertEquals('241', $inutilizacao->getStatus());
            throw $e;
        } finally {
            \NFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testNormalization()
    {
        $inutilizacao = self::criaInutilizacao();
        $inutilizacao->setModelo('55');
        $this->assertEquals(Nota::MODELO_NFE, $inutilizacao->getModelo());
        $this->assertEquals('55', $inutilizacao->getModelo(true));
        $inutilizacao->setModelo('50');
        $this->assertEquals('50', $inutilizacao->getModelo(true));
    }

    public function testInutilizaoInvalida()
    {
        $inutilizacao = self::criaInutilizacao();
        $inutilizacao->setModelo('Invalido');
        $dom = $inutilizacao->getNode()->ownerDocument;
        $dom = $inutilizacao->assinar();
        $this->expectException('\Exception');
        $dom = $inutilizacao->validar($dom);
    }
}
