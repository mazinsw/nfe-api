<?php
namespace NFe\Task;

class InutilizacaoTest extends \PHPUnit_Framework_TestCase
{
    private $sefaz;

    protected function setUp()
    {
        $this->sefaz = \NFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function criaInutilizacao()
    {
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
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'inutilizadoPostFunction'));
        try {
            $inutilizacao = self::criaInutilizacao();
            $dom = $inutilizacao->getNode()->ownerDocument;
            $dom = $inutilizacao->assinar($dom);
            $dom = $inutilizacao->validar($dom);
            $dom = $inutilizacao->envia($dom);
            $inutilizacao->fromArray($inutilizacao);
            $inutilizacao->fromArray(null);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
        $this->assertEquals('102', $inutilizacao->getStatus());
        $this->assertEquals('141170000156683', $inutilizacao->getNumero());

        $xml_file = dirname(dirname(__DIR__)).'/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());

        // $dom->formatOutput = true;
        // file_put_contents($xml_file, $dom->saveXML());
    }

    public function testInutilizaRejeitado()
    {
        \NFe\Common\CurlSoap::setPostFunction(array($this, 'rejeitadoPostFunction'));
        $inutilizacao = self::criaInutilizacao();
        $dom = $inutilizacao->getNode()->ownerDocument;
        $dom = $inutilizacao->assinar();
        $dom = $inutilizacao->validar($dom);
        try {
            $this->setExpectedException('\Exception');
            $inutilizacao->envia($dom);
        } catch (Exception $e) {
            \NFe\Common\CurlSoap::setPostFunction(null);
            $this->assertEquals('241', $inutilizacao->getStatus());
        }
        \NFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testInutilizaoInvalida()
    {
        $inutilizacao = self::criaInutilizacao();
        $inutilizacao->setModelo('Invalido');
        $dom = $inutilizacao->getNode()->ownerDocument;
        $dom = $inutilizacao->assinar();
        $this->setExpectedException('\Exception');
        $dom = $inutilizacao->validar($dom);
    }
}
