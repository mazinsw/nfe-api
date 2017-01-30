<?php
/**
 * MIT License
 *
 * Copyright (c) 2016 MZ Desenvolvimento de Sistemas LTDA
 *
 * @author Francimar Alves <mazinsw@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */
namespace NF;

use NF;
use SEFAZ;
use CurlSoap;
use Exception;
use DOMDocument;
use NodeInterface;
use DomainException;

class Envio
{

    const SERVICO_INUTILIZACAO = 'inutilizacao';
    const SERVICO_PROTOCOLO = 'protocolo';
    const SERVICO_STATUS = 'status';
    const SERVICO_CADASTRO = 'cadastro';
    const SERVICO_AUTORIZACAO = 'autorizacao';
    const SERVICO_RETORNO = 'retorno';
    const SERVICO_RECEPCAO = 'recepcao';
    const SERVICO_CONFIRMACAO = 'confirmacao';
    const SERVICO_EVENTO = 'evento';
    const SERVICO_DESTINADAS = 'destinadas';
    const SERVICO_DOWNLOAD = 'download';
    const SERVICO_DISTRIBUICAO = 'distribuicao';

    private $servico;
    private $ambiente;
    private $modelo;
    private $emissao;
    private $conteudo;

    public function __construct($envio = array())
    {
        $this->fromArray($envio);
    }

    public function getServico($normalize = false)
    {
        if (!$normalize) {
            return $this->servico;
        }
        switch ($this->servico) {
            case self::SERVICO_INUTILIZACAO:
                return NF::PORTAL.'/wsdl/NFeInutilizacao';
            case self::SERVICO_PROTOCOLO:
                return NF::PORTAL.'/wsdl/NFeConsulta';
            case self::SERVICO_STATUS:
                return NF::PORTAL.'/wsdl/NFeStatusServico';
            case self::SERVICO_CADASTRO:
                return NF::PORTAL.'/wsdl/CadConsultaCadastro';
            case self::SERVICO_AUTORIZACAO:
                return NF::PORTAL.'/wsdl/NFeAutorizacao';
            case self::SERVICO_RETORNO:
                return NF::PORTAL.'/wsdl/NFeRetAutorizacao';
            case self::SERVICO_RECEPCAO:
                return NF::PORTAL.'/wsdl/NFeRecepcao';
            case self::SERVICO_CONFIRMACAO:
                return NF::PORTAL.'/wsdl/NFeRetRecepcao';
            case self::SERVICO_EVENTO:
                return NF::PORTAL.'/wsdl/RecepcaoEvento';
            case self::SERVICO_DESTINADAS:
                return NF::PORTAL.'/wsdl/NFeConsultaDest';
            case self::SERVICO_DOWNLOAD:
                return NF::PORTAL.'/wsdl/NFeDownloadNF';
            case self::SERVICO_DISTRIBUICAO:
                return NF::PORTAL.'/wsdl/NFeDistribuicaoDFe';
        }
        return $this->servico;
    }

    public function setServico($servico)
    {
        $this->servico = $servico;
        return $this;
    }

    public function getAmbiente()
    {
        return $this->ambiente;
    }

    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
        return $this;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
        return $this;
    }

    public function getEmissao()
    {
        return $this->emissao;
    }

    public function setEmissao($emissao)
    {
        $this->emissao = $emissao;
        return $this;
    }

    public function getConteudo()
    {
        return $this->conteudo;
    }

    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
        return $this;
    }

    public function getVersao()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $db = $config->getBanco();
        $estado = $config->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $info = $db->getInformacaoServico(
            $this->getEmissao(),
            $estado->getUF(),
            $this->getModelo(),
            $this->getAmbiente()
        );
        if (!isset($info[$this->getServico()])) {
            throw new Exception('O serviço "'.$this->getServico().
                '" não está disponível para o estado "'.$estado->getUF().'"', 404);
        }
        $url = $info[$this->getServico()];
        if (is_array($url)) {
            return $url['versao'];
        }
        return NF::VERSAO;
    }

    public function toArray()
    {
        $envio = array();
        $envio['servico'] = $this->getServico();
        $envio['ambiente'] = $this->getAmbiente();
        $envio['modelo'] = $this->getModelo();
        $envio['emissao'] = $this->getEmissao();
        $envio['conteudo'] = $this->getConteudo();
        return $envio;
    }

    public function fromArray($envio = array())
    {
        if ($envio instanceof Envio) {
            $envio = $envio->toArray();
        } elseif (!is_array($envio)) {
            return $this;
        }
        if (isset($envio['servico'])) {
            $this->setServico($envio['servico']);
        } else {
            $this->setServico(null);
        }
        if (isset($envio['ambiente'])) {
            $this->setAmbiente($envio['ambiente']);
        } else {
            $this->setAmbiente(null);
        }
        if (isset($envio['modelo'])) {
            $this->setModelo($envio['modelo']);
        } else {
            $this->setModelo(null);
        }
        if (isset($envio['emissao'])) {
            $this->setEmissao($envio['emissao']);
        } else {
            $this->setEmissao(null);
        }
        if (isset($envio['conteudo'])) {
            $this->setConteudo($envio['conteudo']);
        } else {
            $this->setConteudo(null);
        }
        return $this;
    }

    private function getNodeHeader()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $estado = $config->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $estado->checkCodigos();
        $doh = new DOMDocument('1.0', 'UTF-8');
        $element = $doh->createElement('nfeCabecMsg');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', $this->getServico(true));
        $element->appendChild($doh->createElement('cUF', $estado->getCodigo(true)));
        $element->appendChild($doh->createElement('versaoDados', $this->getVersao()));
        $doh->appendChild($element);
        return $doh;
    }

    public function getNode($name = null)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'nfeDadosMsg':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', $this->getServico(true));
        // Corrige xmlns:default
        // $data = $dom->importNode($this->getConteudo()->documentElement, true);
        // $element->appendChild($data);
        $element->appendChild($dom->createElement('Conteudo', 0));

        $dom->appendChild($element);

        // Corrige xmlns:default
        // return $dom;
        if ($this->getConteudo() instanceof DOMDocument) {
            $xml = $this->getConteudo()->saveXML($this->getConteudo()->documentElement);
        } else {
            $xml = $this->getConteudo();
        }
        $xml = str_replace('<Conteudo>0</Conteudo>', $xml, $dom->saveXML($dom->documentElement));
        $dom->loadXML($xml);
        return $dom;
    }

    public function envia()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $db = $config->getBanco();
        $estado = $config->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $info = $db->getInformacaoServico(
            $this->getEmissao(),
            $estado->getUF(),
            $this->getModelo(),
            $this->getAmbiente()
        );
        if (!isset($info[$this->getServico()])) {
            throw new Exception('O serviço "'.$this->getServico().
                '" não está disponível para o estado "'.$estado->getUF().'"', 404);
        }
        $url = $info[$this->getServico()];
        if (is_array($url)) {
            $url = $url['url'];
        }
        if ($config->isOffline()) {
            throw new DomainException('Operação offline, sem conexão com a internet', 7);
        }
        $soap = new CurlSoap();
        $soap->setConnectTimeout(intval($config->getTempoLimite()));
        $soap->setTimeout(ceil($config->getTempoLimite() * 1.5));
        $soap->setCertificate($config->getArquivoChavePublica());
        $soap->setPrivateKey($config->getArquivoChavePrivada());
        $doh = $this->getNodeHeader();
        $dob = $this->getNode();
        try {
            $resp = $soap->send($url, $dob, $doh);
            return $resp;
        } catch (DomainException $e) {
            $config->setOffline();
            throw $e;
        }
    }
}
