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
    const QRCODE_VERSAO = '100';

    /**
     * Texto com o QR-Code impresso no DANFE NFC-e
     */
    private $qrcode_url;

    /**
     * Constroi uma instância de NFCe vazia
     * @param  array $nfce Array contendo dados do NFCe
     */
    public function __construct($nfce = array())
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
     * URL da página de consulta da nota fiscal
     * @param boolean $normalize informa se a URL de consulta deve estar no formato do XML
     * @return string URL de consulta da NFCe
     */
    public function getConsultaURL($normalize = false)
    {
        $estado = $this->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $info = $db->getInformacaoServico(
            $this->getEmissao(),
            $estado->getUF(),
            $this->getModelo(),
            $this->getAmbiente()
        );
        if (!isset($info['consulta'])) {
            throw new \Exception('Não existe URL de consulta da nota para o estado "'.$estado->getUF().'"', 404);
        }
        $url = $info['consulta'];
        if (is_array($url)) {
            $url = $url['url'];
        }
        return $url;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $nfce = parent::toArray($recursive);
        $nfce['qrcode_url'] = $this->getQRCodeURL();
        return $nfce;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $nfce Array ou instância de NFCe, para copiar os valores
     * @return NFCe A própria instância da classe
     */
    public function fromArray($nfce = array())
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
        return $this;
    }

    private function gerarQRCodeInfo(&$dom)
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $totais = $this->getTotais();
        // if ($this->getEmissao() == self::EMISSAO_NORMAL) {
            $dig_val = Util::loadNode($dom, 'DigestValue', 'Tag "DigestValue" não encontrada na NFCe');
        // } else {
        //     $dig_val = base64_encode(sha1($dom->saveXML(), true));
        // }
        $params = array(
            'chNFe' => $this->getID(),
            'nVersao' => self::QRCODE_VERSAO,
            'tpAmb' => $this->getAmbiente(true),
            'cDest' => null,
            'dhEmi' => Util::toHex($this->getDataEmissao(true)),
            'vNF' => Util::toCurrency($totais['nota']),
            'vICMS' => Util::toCurrency($totais[Imposto::GRUPO_ICMS]),
            'digVal' => Util::toHex($dig_val),
            'cIdToken' => Util::padDigit($config->getToken(), 6),
            'cHashQRCode' => null
        );
        if (!is_null($this->getDestinatario())) {
            $params['cDest'] = $this->getDestinatario()->getID(true);
        } else {
            unset($params['cDest']);
        }
        $_params = $params;
        unset($_params['cHashQRCode']);
        $query = http_build_query($_params);
        $params['cHashQRCode'] = sha1($query.$config->getCSC());
        return $params;
    }

    private function checkQRCode(&$dom)
    {
        $estado = $this->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $params = $this->gerarQRCodeInfo($dom);
        $query = http_build_query($params);
        $info = $db->getInformacaoServico(
            $this->getEmissao(),
            $estado->getUF(),
            $this->getModelo(),
            $this->getAmbiente()
        );
        if (!isset($info['qrcode'])) {
            throw new \Exception('Não existe URL de consulta de QRCode para o estado "'.$estado->getUF().'"', 404);
        }
        $url = $info['qrcode'];
        if (is_array($url)) {
            $url = $url['url'];
        }
        $url .= (strpos($url, '?') === false?'?':'&').$query;
        $this->setQRCodeURL($url);
    }

    private function getNodeSuplementar(&$dom)
    {
        $this->checkQRCode($dom);
        $element = $dom->createElement('infNFeSupl');
        $qrcode = $dom->createElement('qrCode');
        $data = $dom->createCDATASection($this->getQRCodeURL(true));
        $qrcode->appendChild($data);
        $element->appendChild($qrcode);
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
