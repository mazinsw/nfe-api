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
namespace NFe\Core;

use NFe\Common\Util;
use NFe\Entity\Imposto;

/**
 * Classe para validação da nota fiscal eletrônica do consumidor
 */
class NFCe extends Nota
{

    /**
     * Versão do QRCode
     */
    const QRCODE_VERSAO = '2';

    /**
     * Texto com o QR-Code impresso no DANFE NFC-e
     */
    private $qrcode_url;
    /**
     * Informar a URL da "Consulta por chave de acesso da NFC-e". A mesma URL
     * que deve estar informada no DANFE NFC-e para consulta por chave de
     * acesso.
     */
    private $consulta_url;

    /**
     * Constroi uma instância de NFCe vazia
     * @param  array $nfce Array contendo dados do NFCe
     */
    public function __construct($nfce = [])
    {
        parent::__construct($nfce);
        $this->setModelo(self::MODELO_NFCE);
        $this->setFormato(self::FORMATO_CONSUMIDOR);
    }

    /**
     * Texto com o QR-Code impresso no DANFE NFC-e
     * @param boolean $normalize informa se a qrcode_url deve estar no formato do XML
     * @return mixed qrcode_url do NFCe
     */
    public function getQRCodeURL($normalize = false)
    {
        if (!$normalize) {
            return $this->qrcode_url;
        }
        return $this->qrcode_url;
    }
    
    /**
     * Altera o valor da QrcodeURL para o informado no parâmetro
     * @param mixed $qrcode_url novo valor para QrcodeURL
     * @return NFCe A própria instância da classe
     */
    public function setQRCodeURL($qrcode_url)
    {
        $this->qrcode_url = $qrcode_url;
        return $this;
    }

    /**
     * Informar a URL da "Consulta por chave de acesso da NFC-e". A mesma URL
     * que deve estar informada no DANFE NFC-e para consulta por chave de
     * acesso.
     * @param boolean $normalize informa se o consulta_url deve estar no formato do XML
     * @return mixed consulta_url do NFCe
     */
    public function getConsultaURL($normalize = false)
    {
        if (!$normalize) {
            return $this->consulta_url;
        }
        return $this->consulta_url;
    }
    
    /**
     * Altera o valor do ConsultaURL para o informado no parâmetro
     * @param mixed $consulta_url novo valor para ConsultaURL
     * @return NFCe A própria instância da classe
     */
    public function setConsultaURL($consulta_url)
    {
        $this->consulta_url = $consulta_url;
        return $this;
    }

    /**
     * URL da página do QRCode e consulta da nota fiscal
     * @return array URL do QRCode e consulta da NFCe
     */
    private function getURLs()
    {
        $estado = $this->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $info = $db->getInformacaoServico(
            $this->getEmissao(),
            $estado->getUF(),
            $this->getModelo(),
            $this->getAmbiente()
        );
        return $info;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $nfce = parent::toArray($recursive);
        $nfce['qrcode_url'] = $this->getQRCodeURL();
        $nfce['consulta_url'] = $this->getConsultaURL();
        return $nfce;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $nfce Array ou instância de NFCe, para copiar os valores
     * @return NFCe A própria instância da classe
     */
    public function fromArray($nfce = [])
    {
        if ($nfce instanceof NFCe) {
            $nfce = $nfce->toArray();
        } elseif (!is_array($nfce)) {
            return $this;
        }
        parent::fromArray($nfce);
        if (!isset($nfce['qrcode_url'])) {
            $this->setQRCodeURL(null);
        } else {
            $this->setQRCodeURL($nfce['qrcode_url']);
        }
        if (!isset($nfce['consulta_url'])) {
            $this->setConsultaURL(null);
        } else {
            $this->setConsultaURL($nfce['consulta_url']);
        }
        return $this;
    }

    private function makeUrlQuery($dom)
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $totais = $this->getTotais();
        if ($this->getEmissao() == self::EMISSAO_NORMAL) {
            $params = [
                $this->getID(), // chave de acesso
                self::QRCODE_VERSAO, // versão do QR Code
                $this->getAmbiente(true), // Identificação do ambiente
                intval($config->getToken()), // Identificador do CSC (Sem zeros não significativos)
            ];
        } else { // contingência
            $dig_val = Util::loadNode($dom, 'DigestValue', 'Tag "DigestValue" não encontrada na NFCe');
            $params = [
                $this->getID(), // chave de acesso
                self::QRCODE_VERSAO, // versão do QR Code
                $this->getAmbiente(true), // Identificação do ambiente
                date('d', $this->getDataEmissao()), // dia da data de emissão
                Util::toCurrency($totais['nota']), // valor total da NFC-e
                Util::toHex($dig_val), // DigestValue da NFC-e
                intval($config->getToken()), // Identificador do CSC (Sem zeros não significativos)
            ];
        }
        $query = implode('|', $params);
        $hash = sha1($query.$config->getCSC());
        $params = [$query, $hash];
        $query = implode('|', $params);
        return ['p' => $query];
    }

    private function buildURLs($dom)
    {
        $info = $this->getURLs();
        if (!isset($info['qrcode'])) {
            throw new \Exception('Não existe URL de consulta de QRCode para o estado "'.$estado->getUF().'"', 404);
        }
        $url = $info['qrcode'];
        if (is_array($url)) {
            $url = $url['url'];
        }
        $params = $this->makeUrlQuery($dom);
        $query = urldecode(http_build_query($params));
        $url .= (strpos($url, '?') === false?'?':'&').$query;
        $this->setQRCodeURL($url);
        if (!isset($info['consulta'])) {
            throw new \Exception('Não existe URL de consulta da nota para o estado "'.$estado->getUF().'"', 404);
        }
        $url = $info['consulta'];
        if (is_array($url)) {
            $url = $url['url'];
        }
        $this->setConsultaURL($url);
    }

    private function getNodeSuplementar($dom)
    {
        $this->buildURLs($dom);
        $element = $dom->createElement('infNFeSupl');
        $qrcode = $dom->createElement('qrCode');
        $data = $dom->createCDATASection($this->getQRCodeURL(true));
        $qrcode->appendChild($data);
        $element->appendChild($qrcode);
        $urlchave = $dom->createElement('urlChave');
        $data = $dom->createCDATASection($this->getConsultaURL(true));
        $urlchave->appendChild($data);
        $element->appendChild($urlchave);
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode($element, $name = null)
    {
        $element = parent::loadNode($element, $name);
        $qrcode_url = Util::loadNode($element, 'qrCode');
        if (Util::nodeExists($element, 'Signature') && is_null($qrcode_url)) {
            throw new \Exception('Tag "qrCode" não encontrada na NFCe', 404);
        }
        $this->setQRCodeURL($qrcode_url);
        $consulta_url = Util::loadNode($element, 'urlChave');
        $this->setConsultaURL($consulta_url);
        return $element;
    }

    /**
     * Assina e adiciona informações suplementares da nota
     */
    public function assinar($dom = null)
    {
        $dom = parent::assinar($dom);
        $suplementar = $this->getNodeSuplementar($dom);
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        $signature->parentNode->insertBefore($suplementar, $signature);
        return $dom;
    }
}
