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
namespace NFe\Task;

use NFe\Core\Nota;
use NFe\Core\SEFAZ;
use NFe\Common\Node;
use NFe\Common\Util;
use NFe\Common\CurlSoap;

/**
 * Envia requisições para os servidores da SEFAZ
 */
class Envio
{

    /**
     * Tipo de serviço a ser executado
     */
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

    /**
     * Tipo de serviço a ser executado
     */
    private $servico;
    /**
     * Identificação do Ambiente:
     * 1 - Produção
     * 2 - Homologação
     */
    private $ambiente;
    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     */
    private $modelo;
    /**
     * Forma de emissão da NF-e
     */
    private $emissao;
    /**
     * Conteudo a ser enviado
     */
    private $conteudo;

    /**
     * Constroi uma instância de Envio vazia
     * @param  array $envio Array contendo dados do Envio
     */
    public function __construct($envio = array())
    {
        $this->fromArray($envio);
    }

    /**
     * Tipo de serviço a ser executado
     * @param boolean $normalize informa se o servico deve estar no formato do XML
     * @return mixed servico do Envio
     */
    public function getServico($normalize = false)
    {
        if (!$normalize) {
            return $this->servico;
        }
        $url = $this->getServiceInfo();
        if (is_array($url) && isset($url['servico'])) {
            return Nota::PORTAL.'/wsdl/'.$url['servico'];
        }
        throw new \Exception('A ação do serviço "'.$this->getServico().'" não foi configurada', 404);
    }

    /**
     * Altera o valor do Servico para o informado no parâmetro
     * @param mixed $servico novo valor para Servico
     * @return Envio A própria instância da classe
     */
    public function setServico($servico)
    {
        $this->servico = $servico;
        return $this;
    }

    /**
     * Identificação do Ambiente:
     * 1 - Produção
     * 2 - Homologação
     * @param boolean $normalize informa se o ambiente deve estar no formato do XML
     * @return mixed ambiente do Envio
     */
    public function getAmbiente($normalize = false)
    {
        if (!$normalize) {
            return $this->ambiente;
        }
        switch ($this->ambiente) {
            case Nota::AMBIENTE_PRODUCAO:
                return '1';
            case Nota::AMBIENTE_HOMOLOGACAO:
                return '2';
        }
        return $this->ambiente;
    }

    /**
     * Altera o valor do Ambiente para o informado no parâmetro
     * @param mixed $ambiente novo valor para Ambiente
     * @return Envio A própria instância da classe
     */
    public function setAmbiente($ambiente)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     * @param boolean $normalize informa se o modelo deve estar no formato do XML
     * @return mixed modelo do Envio
     */
    public function getModelo($normalize = false)
    {
        if (!$normalize) {
            return $this->modelo;
        }
        switch ($this->modelo) {
            case Nota::MODELO_NFE:
                return '55';
            case Nota::MODELO_NFCE:
                return '65';
        }
        return $this->modelo;
    }

    /**
     * Altera o valor do Modelo para o informado no parâmetro
     * @param mixed $modelo novo valor para Modelo
     * @return Envio A própria instância da classe
     */
    public function setModelo($modelo)
    {
        switch ($modelo) {
            case '55':
                $modelo = Nota::MODELO_NFE;
                break;
            case '65':
                $modelo = Nota::MODELO_NFCE;
                break;
        }
        $this->modelo = $modelo;
        return $this;
    }

    /**
     * Forma de emissão da NF-e
     * @param boolean $normalize informa se o emissao deve estar no formato do XML
     * @return mixed emissao do Envio
     */
    public function getEmissao($normalize = false)
    {
        if (!$normalize) {
            return $this->emissao;
        }
        switch ($this->emissao) {
            case Nota::EMISSAO_NORMAL:
                return '1';
            case Nota::EMISSAO_CONTINGENCIA:
                return '9';
        }
        return $this->emissao;
    }

    /**
     * Altera o valor do Emissao para o informado no parâmetro
     * @param mixed $emissao novo valor para Emissao
     * @return Envio A própria instância da classe
     */
    public function setEmissao($emissao)
    {
        switch ($emissao) {
            case '1':
                $emissao = Nota::EMISSAO_NORMAL;
                break;
            case '9':
                $emissao = Nota::EMISSAO_CONTINGENCIA;
                break;
        }
        $this->emissao = $emissao;
        return $this;
    }

    /**
     * Conteudo a ser enviado
     * @return mixed conteudo do Envio
     */
    public function getConteudo()
    {
        return $this->conteudo;
    }

    /**
     * Altera o valor do Conteudo para o informado no parâmetro
     * @param mixed $conteudo novo valor para Conteudo
     * @return Envio A própria instância da classe
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
        return $this;
    }

    /**
     * Obtém a versão do serviço a ser utilizado
     * @return string Versão do serviço
     */
    public function getVersao()
    {
        $url = $this->getServiceInfo();
        if (is_array($url) && isset($url['versao'])) {
            return $url['versao'];
        }
        return Nota::VERSAO;
    }

    /**
     * Devolve um array com as informações de serviço (URL, Versão, Serviço)
     * @return array Informações de serviço
     */
    private function getServiceInfo()
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
            throw new \Exception('O serviço "'.$this->getServico().
                '" não está disponível para o estado "'.$estado->getUF().'"', 404);
        }
        return $info[$this->getServico()];
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $envio = array();
        $envio['servico'] = $this->getServico();
        $envio['ambiente'] = $this->getAmbiente();
        $envio['modelo'] = $this->getModelo();
        $envio['emissao'] = $this->getEmissao();
        $envio['conteudo'] = $this->getConteudo();
        return $envio;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $envio Array ou instância de Envio, para copiar os valores
     * @return Envio A própria instância da classe
     */
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

    /**
     * Obtém o nó do cabeçalho da requisição SOAP
     * @return DOMDocument Cabeçalho para adicionar na requisição
     */
    private function getNodeHeader()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $estado = $config->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $estado->checkCodigos();
        $doh = new \DOMDocument('1.0', 'UTF-8');
        $element = $doh->createElement('nfeCabecMsg');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', $this->getServico(true));
        Util::appendNode($element, 'cUF', $estado->getCodigo(true));
        Util::appendNode($element, 'versaoDados', $this->getVersao());
        $doh->appendChild($element);
        return $doh;
    }

    /**
     * Cria um nó XML do envio de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'nfeDadosMsg':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', $this->getServico(true));
        // Corrige xmlns:default
        // $data = $dom->importNode($this->getConteudo()->documentElement, true);
        // $element->appendChild($data);
        Util::appendNode($element, 'Conteudo', 0);

        $dom->appendChild($element);

        // Corrige xmlns:default
        // return $dom;
        if ($this->getConteudo() instanceof \DOMDocument) {
            $xml = $this->getConteudo()->saveXML($this->getConteudo()->documentElement);
        } else {
            $xml = $this->getConteudo();
        }
        $xml = str_replace('<Conteudo>0</Conteudo>', $xml, $dom->saveXML($dom->documentElement));
        $dom->loadXML($xml);
        return $dom;
    }

    /**
     * Envia o conteúdo para o serviço da SEFAZ informado
     * @return DOMDocument Documento XML da resposta da SEFAZ
     */
    public function envia()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $url = $this->getServiceInfo();
        if (is_array($url)) {
            $url = $url['url'];
        }
        if ($config->isOffline()) {
            throw new \NFe\Exception\NetworkException('Operação offline, sem conexão com a internet', 7);
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
        } catch (\NFe\Exception\NetworkException $e) {
            $config->setOffline(time());
            throw $e;
        }
    }
}
