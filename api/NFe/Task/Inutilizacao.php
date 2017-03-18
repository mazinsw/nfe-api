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
use NFe\Common\Util;
use NFe\Exception\ValidationException;
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

    public function toArray($recursive = false)
    {
        $inutilizacao = parent::toArray($recursive);
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

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'inutNFe':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $element->appendChild($versao);

        $info = $dom->createElement('infInut');
        $id = $dom->createAttribute('Id');
        $id->value = $this->getID(true);
        $info->appendChild($id);

        Util::appendNode($info, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($info, 'xServ', 'INUTILIZAR');
        Util::appendNode($info, 'cUF', $this->getUF(true));
        Util::appendNode($info, 'ano', $this->getAno(true));
        Util::appendNode($info, 'CNPJ', $this->getCNPJ(true));
        Util::appendNode($info, 'mod', $this->getModelo(true));
        Util::appendNode($info, 'serie', $this->getSerie(true));
        Util::appendNode($info, 'nNFIni', $this->getInicio(true));
        Util::appendNode($info, 'nNFFin', $this->getFinal(true));
        Util::appendNode($info, 'xJust', $this->getJustificativa(true));
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
        Util::appendNode($info, 'nProt', $this->getNumero(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'infInut':$name;
        $element = parent::loadNode($element, $name);
        if (!$this->isInutilizado()) {
            return $element;
        }
        $this->setAno(Util::loadNode($element, 'ano'));
        $this->setCNPJ(Util::loadNode($element, 'CNPJ'));
        $this->setModelo(Util::loadNode($element, 'mod'));
        $this->setSerie(Util::loadNode($element, 'serie'));
        $this->setInicio(Util::loadNode($element, 'nNFIni'));
        $this->setFinal(Util::loadNode($element, 'nNFFin'));
        $this->setNumero(Util::loadNode($element, 'nProt'));
        return $element;
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_INUTILIZACAO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setConteudo($dom);
        $resp = $envio->envia();
        $this->loadNode($resp);
        if (!$this->isInutilizado()) {
            throw new \Exception($this->getMotivo(), $this->getStatus());
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
        $xsd_path = dirname(__DIR__) . '/Core/schema';
        $xsd_file = $xsd_path . '/inutNFe_v3.10.xsd';
        if (!file_exists($xsd_file)) {
            throw new \Exception('O arquivo "'.$xsd_file.'" de esquema XSD não existe!', 404);
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
