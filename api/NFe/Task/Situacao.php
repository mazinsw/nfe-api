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
use NFe\Common\Util;
use NFe\Exception\ValidationException;

class Situacao extends Retorno
{

    private $chave;
    private $modelo;

    const TAG_RETORNO = 'retConsSitNFe';

    public function __construct($situacao = array())
    {
        parent::__construct($situacao);
    }

    /**
     * Chaves de acesso da NF-e, compostas por: UF do emitente, AAMM da emissão
     * da NFe, CNPJ do emitente, modelo, série e número da NF-e e código
     * numérico+DV.
     */
    public function getChave($normalize = false)
    {
        if (!$normalize) {
            return $this->chave;
        }
        return $this->chave;
    }

    public function setChave($chave)
    {
        $this->chave = $chave;
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

    public function toArray($recursive = false)
    {
        $situacao = parent::toArray($recursive);
        $situacao['chave'] = $this->getChave();
        $situacao['modelo'] = $this->getModelo();
        return $situacao;
    }

    public function fromArray($situacao = array())
    {
        if ($situacao instanceof Situacao) {
            $situacao = $situacao->toArray();
        } elseif (!is_array($situacao)) {
            return $this;
        }
        parent::fromArray($situacao);
        if (isset($situacao['chave'])) {
            $this->setChave($situacao['chave']);
        } else {
            $this->setChave(null);
        }
        if (isset($situacao['modelo'])) {
            $this->setModelo($situacao['modelo']);
        } else {
            $this->setModelo(null);
        }
        return $this;
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_PROTOCOLO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setConteudo($dom);
        $resp = $envio->envia();
        $this->loadNode($resp);
        if ($this->isAutorizado()) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($resp);
            return $protocolo;
        } elseif ($this->isCancelado()) {
            $evento = new Evento();
            $evento->loadStatusNode($resp, self::TAG_RETORNO);
            $evento->loadNode($resp);
            return $evento;
        }
        return $this;
    }

    public function consulta($nota = null)
    {
        if (!is_null($nota)) {
            $this->setChave($nota->getID());
            $this->setAmbiente($nota->getAmbiente());
            $this->setModelo($nota->getModelo());
        }
        $dom = $this->getNode()->ownerDocument;
        $dom = $this->validar($dom);
        $retorno = $this->envia($dom);
        if ($retorno instanceof Protocolo && $retorno->isAutorizado() && !is_null($nota)) {
            $nota->setProtocolo($retorno);
        }
        return $retorno;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'consSitNFe':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $element->appendChild($versao);

        Util::appendNode($element, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($element, 'xServ', 'CONSULTAR');
        Util::appendNode($element, 'chNFe', $this->getChave(true));
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?self::TAG_RETORNO:$name;
        $element = parent::loadNode($element, $name);
        $this->setChave(Util::loadNode($element, 'chNFe'));
        return $element;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(__DIR__) . '/Core/schema';
        $xsd_file = $xsd_path . '/consSitNFe_v3.10.xsd';
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
