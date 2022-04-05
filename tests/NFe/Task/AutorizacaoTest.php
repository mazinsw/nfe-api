<?php

namespace NFe\Task;

use NFe\Core\Nota;

class AutorizacaoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function processaPostFunction($test, $soap_curl, $url, $data, $xml_name, $resp_name)
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/' . $xml_name;
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($data);

        // idLote auto gerado, copia para testar
        $node_cmp = \NFe\Common\Util::findNode($dom_cmp, 'idLote');
        $node = \NFe\Common\Util::findNode($dom, 'idLote');
        $node_cmp->nodeValue = $node->nodeValue;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $xml_cmp = $dom_cmp->saveXML();
        $test->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML());

        $xml_resp_file = dirname(dirname(__DIR__)) . '/resources/xml/task/' . $resp_name;
        $dom_resp = new \DOMDocument();
        $dom_resp->preserveWhiteSpace = false;
        $dom_resp->load($xml_resp_file);

        $soap_curl->response = $dom_resp;
    }

    public function autorizadoPostFunction($soap_curl, $url, $data)
    {
        self::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaAutorizadoReponseSOAP.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        self::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaRejeicaoReponseSOAP.xml'
        );
    }

    public function processamentoPostFunction($soap_curl, $url, $data)
    {
        self::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaProcessamentoReponseSOAP.xml'
        );
    }

    public function testAutorizaAutorizado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'autorizadoPostFunction']);
        try {
            $autorizacao = new Autorizacao();
            $retorno = $autorizacao->envia($nota, $dom);
            $autorizacao->fromArray($autorizacao);
            $autorizacao->fromArray($autorizacao->toArray());
            $autorizacao->fromArray(null);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Protocolo', $retorno);
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testAutorizaRejeitado()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        try {
            $autorizacao = new Autorizacao();
            $retorno = $autorizacao->envia($nota, $dom);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Autorizacao', $retorno);
        $this->assertEquals('785', $retorno->getStatus());
    }

    public function testAutorizaProcessamento()
    {
        $data = \NFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \NFe\Common\CurlSoap::setPostFunction([$this, 'processamentoPostFunction']);
        try {
            $autorizacao = new Autorizacao();
            $retorno = $autorizacao->envia($nota, $dom);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertInstanceOf('\\NFe\\Task\\Recibo', $retorno);
        $this->assertEquals('103', $retorno->getStatus());
    }

    public function testValidarEsquemaNotFound()
    {
        $autorizacao = new Autorizacao();
        $this->expectException('\Exception');
        $autorizacao->validar('<schema/>');
    }

    public function testNaoValidado()
    {
        $autorizacao = new Autorizacao();
        $autorizacao->setVersao(Nota::VERSAO);
        $this->expectException('\NFe\Exception\ValidationException');
        $autorizacao->validar('<schema/>');
    }
}
