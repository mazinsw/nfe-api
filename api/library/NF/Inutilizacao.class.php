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
use Estado;
use Exception;
use DOMDocument;
use ValidationException;
use FR3D\XmlDSig\Adapter\AdapterInterface;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;

class Inutilizacao extends Retorno
{

    private $id;
    private $ano;
    private $cnpj;
    private $modelo;
    private $serie;
    private $inicio;
    private $final;
    private $justificativa;
    private $numero;

    public function __construct($inutilizacao = array())
    {
        parent::__construct($inutilizacao);
    }

    /**
     * Formado por:
     * ID = Literal
     * 43 = Código Estado
     * 15 = Ano
     *
     * 00000000000000 = CNPJ
     * 55 = Modelo
     * 001 = Série
     * 000000411 =
     * Número Inicial
     * 000000411 = Número Final
     */
    public function getID($normalize = false)
    {
        if (!$normalize) {
            return $this->id;
        }
        return 'ID'.$this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getAno($normalize = false)
    {
        if (!$normalize) {
            return $this->ano;
        }
        return $this->ano % 100;
    }

    public function setAno($ano)
    {
        $this->ano = $ano;
        return $this;
    }

    public function getCNPJ($normalize = false)
    {
        if (!$normalize) {
            return $this->cnpj;
        }
        return $this->cnpj;
    }

    public function setCNPJ($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    public function getModelo($normalize = false)
    {
        if (!$normalize) {
            return $this->modelo;
        }
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
        return $this;
    }

    public function getSerie($normalize = false)
    {
        if (!$normalize) {
            return $this->serie;
        }
        return $this->serie;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    public function getInicio($normalize = false)
    {
        if (!$normalize) {
            return $this->inicio;
        }
        return $this->inicio;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function getFinal($normalize = false)
    {
        if (!$normalize) {
            return $this->final;
        }
        return $this->final;
    }

    public function setFinal($final)
    {
        $this->final = $final;
        return $this;
    }

    public function getJustificativa($normalize = false)
    {
        if (!$normalize) {
            return $this->justificativa;
        }
        return $this->justificativa;
    }

    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
        return $this;
    }

    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Informa se os números foram inutilizados
     */
    public function isInutilizado()
    {
        return $this->getStatus() == '102';
    }

    public function toArray()
    {
        $inutilizacao = parent::toArray();
        $inutilizacao['id'] = $this->getID();
        $inutilizacao['ano'] = $this->getAno();
        $inutilizacao['cnpj'] = $this->getCNPJ();
        $inutilizacao['modelo'] = $this->getModelo();
        $inutilizacao['serie'] = $this->getSerie();
        $inutilizacao['inicio'] = $this->getInicio();
        $inutilizacao['final'] = $this->getFinal();
        $inutilizacao['justificativa'] = $this->getJustificativa();
        $inutilizacao['numero'] = $this->getNumero();
        return $inutilizacao;
    }

    public function fromArray($inutilizacao = array())
    {
        if ($inutilizacao instanceof Inutilizacao) {
            $inutilizacao = $inutilizacao->toArray();
        } elseif (!is_array($inutilizacao)) {
            return $this;
        }
        parent::fromArray($inutilizacao);
        if (isset($inutilizacao['id'])) {
            $this->setID($inutilizacao['id']);
        } else {
            $this->setID(null);
        }
        if (isset($inutilizacao['ano'])) {
            $this->setAno($inutilizacao['ano']);
        } else {
            $this->setAno(null);
        }
        if (isset($inutilizacao['cnpj'])) {
            $this->setCNPJ($inutilizacao['cnpj']);
        } else {
            $this->setCNPJ(null);
        }
        if (isset($inutilizacao['modelo'])) {
            $this->setModelo($inutilizacao['modelo']);
        } else {
            $this->setModelo(null);
        }
        if (isset($inutilizacao['serie'])) {
            $this->setSerie($inutilizacao['serie']);
        } else {
            $this->setSerie(null);
        }
        if (isset($inutilizacao['inicio'])) {
            $this->setInicio($inutilizacao['inicio']);
        } else {
            $this->setInicio(null);
        }
        if (isset($inutilizacao['final'])) {
            $this->setFinal($inutilizacao['final']);
        } else {
            $this->setFinal(null);
        }
        if (isset($inutilizacao['justificativa'])) {
            $this->setJustificativa($inutilizacao['justificativa']);
        } else {
            $this->setJustificativa(null);
        }
        if (isset($inutilizacao['numero'])) {
            $this->setNumero($inutilizacao['numero']);
        } else {
            $this->setNumero(null);
        }
        return $this;
    }

    public function gerarID()
    {
        $id = sprintf(
            '%02d%02d%s%02d%03d%09d%09d',
            $this->getUF(true),
            $this->getAno(true), // 2 dígitos
            $this->getCNPJ(true),
            $this->getModelo(true),
            $this->getSerie(true),
            $this->getInicio(true),
            $this->getFinal(true)
        );
        return $id;
    }

    public function getNode($name = null)
    {
        $this->setID($this->gerarID());

        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'inutNFe':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NF::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = NF::VERSAO;
        $element->appendChild($versao);

        $info = $dom->createElement('infInut');
        $id = $dom->createAttribute('Id');
        $id->value = $this->getID(true);
        $info->appendChild($id);

        $info->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
        $info->appendChild($dom->createElement('xServ', 'INUTILIZAR'));
        $info->appendChild($dom->createElement('cUF', $this->getUF(true)));
        $info->appendChild($dom->createElement('ano', $this->getAno(true)));
        $info->appendChild($dom->createElement('CNPJ', $this->getCNPJ(true)));
        $info->appendChild($dom->createElement('mod', $this->getModelo(true)));
        $info->appendChild($dom->createElement('serie', $this->getSerie(true)));
        $info->appendChild($dom->createElement('nNFIni', $this->getInicio(true)));
        $info->appendChild($dom->createElement('nNFFin', $this->getFinal(true)));
        $info->appendChild($dom->createElement('xJust', $this->getJustificativa(true)));
        $element->appendChild($info);
        $dom->appendChild($element);
        return $element;
    }

    public function getReturnNode()
    {
        $outros = parent::getNode('infInut');
        $element = $this->getNode('retInutNFe');
        $dom = $element->ownerDocument;
        $info = $dom->getElementsByTagName('infInut')->item(0);
        $info->removeAttribute('Id');
        $removeTags = array('tpAmb', 'xServ', 'xJust');
        foreach ($removeTags as $key) {
            $node = $info->getElementsByTagName($key)->item(0);
            $info->removeChild($node);
        }
        $uf = $info->getElementsByTagName('cUF')->item(0);
        foreach ($outros->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $info->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                continue;
            }
            switch ($node->nodeName) {
                case 'dhRecbto':
                    $info->appendChild($node);
                    break;
                default:
                    $info->insertBefore($node, $uf);
            }
        }
        $info->appendChild($dom->createElement('nProt', $this->getNumero(true)));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'infInut':$name;
        $element = parent::loadNode($element, $name);
        if (!$this->isInutilizado()) {
            return $element;
        }
        $_fields = $element->getElementsByTagName('ano');
        if ($_fields->length > 0) {
            $ano = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "ano" do campo "Ano" não encontrada', 404);
        }
        $this->setAno($ano);
        $_fields = $element->getElementsByTagName('CNPJ');
        if ($_fields->length > 0) {
            $cnpj = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "CNPJ" do campo "CNPJ" não encontrada', 404);
        }
        $this->setCNPJ($cnpj);
        $_fields = $element->getElementsByTagName('mod');
        if ($_fields->length > 0) {
            $modelo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "mod" do campo "Modelo" não encontrada', 404);
        }
        $this->setModelo($modelo);
        $_fields = $element->getElementsByTagName('serie');
        if ($_fields->length > 0) {
            $serie = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "serie" do campo "Serie" não encontrada', 404);
        }
        $this->setSerie($serie);
        $_fields = $element->getElementsByTagName('nNFIni');
        if ($_fields->length > 0) {
            $inicio = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "nNFIni" do campo "Inicio" não encontrada', 404);
        }
        $this->setInicio($inicio);
        $_fields = $element->getElementsByTagName('nNFFin');
        if ($_fields->length > 0) {
            $final = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "nNFFin" do campo "Final" não encontrada', 404);
        }
        $this->setFinal($final);
        $_fields = $element->getElementsByTagName('nProt');
        if ($_fields->length > 0) {
            $numero = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "nProt" do campo "Numero" não encontrada', 404);
        }
        $this->setNumero($numero);
        return $element;
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_INUTILIZACAO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(NF::EMISSAO_NORMAL);
        $envio->setConteudo($dom);
        $resp = $envio->envia();
        $this->loadNode($resp);
        if (!$this->isInutilizado()) {
            throw new Exception($this->getMotivo(), $this->getStatus());
        }
        return $this->getReturnNode()->ownerDocument;
    }

    /**
     * Assina o XML com a assinatura eletrônica do tipo A1
     */
    public function assinar($dom = null)
    {
        if (is_null($dom)) {
            $xml = $this->getNode();
            $dom = $xml->ownerDocument;
        }
        $config = SEFAZ::getInstance()->getConfiguracao();

        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey($config->getChavePrivada());
        $adapter->setPublicKey($config->getChavePublica());
        $adapter->addTransform(AdapterInterface::ENVELOPED);
        $adapter->addTransform(AdapterInterface::XML_C14N);
        $adapter->sign($dom, 'infInut');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(dirname(dirname(__FILE__))) . '/schema';
        $xsd_file = $xsd_path . '/inutNFe_v3.10.xsd';
        if (!file_exists($xsd_file)) {
            throw new Exception('O arquivo "'.$xsd_file.'" de esquema XSD não existe!', 404);
        }
        // Enable user error handling
        $save = libxml_use_internal_errors(true);
        if ($dom->schemaValidate($xsd_file)) {
            libxml_use_internal_errors($save);
            return $dom;
        }
        $msg = array();
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $msg[] = 'Não foi possível validar o XML: '.$error->message;
        }
        libxml_clear_errors();
        libxml_use_internal_errors($save);
        throw new ValidationException($msg);
    }
}
