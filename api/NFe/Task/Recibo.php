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

class Recibo extends Retorno
{

    const INFO_TAGNAME = 'infRec';

    private $numero;
    private $tempo_medio;
    private $codigo;
    private $mensagem;
    private $modelo;

    public function __construct($recibo = array())
    {
        parent::__construct($recibo);
    }

    /**
     * Número do Recibo
     */
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
     * Tempo médio de resposta do serviço (em segundos) dos últimos 5 minutos
     */
    public function getTempoMedio($normalize = false)
    {
        if (!$normalize) {
            return $this->tempo_medio;
        }
        return $this->tempo_medio;
    }

    public function setTempoMedio($tempo_medio)
    {
        $this->tempo_medio = $tempo_medio;
        return $this;
    }

    /**
     * Código da Mensagem (v2.0) alterado para tamanho variavel 1-4.
     * (NT2011/004)
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Mensagem da SEFAZ para o emissor. (v2.0)
     */
    public function getMensagem($normalize = false)
    {
        if (!$normalize) {
            return $this->mensagem;
        }
        return $this->mensagem;
    }

    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
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
        $recibo = parent::toArray($recursive);
        $recibo['numero'] = $this->getNumero();
        $recibo['tempo_medio'] = $this->getTempoMedio();
        $recibo['codigo'] = $this->getCodigo();
        $recibo['mensagem'] = $this->getMensagem();
        $recibo['modelo'] = $this->getModelo();
        return $recibo;
    }

    public function fromArray($recibo = array())
    {
        if ($recibo instanceof Recibo) {
            $recibo = $recibo->toArray();
        } elseif (!is_array($recibo)) {
            return $this;
        }
        parent::fromArray($recibo);
        if (isset($recibo['numero'])) {
            $this->setNumero($recibo['numero']);
        } else {
            $this->setNumero(null);
        }
        if (isset($recibo['tempo_medio'])) {
            $this->setTempoMedio($recibo['tempo_medio']);
        } else {
            $this->setTempoMedio(null);
        }
        if (isset($recibo['codigo'])) {
            $this->setCodigo($recibo['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (isset($recibo['mensagem'])) {
            $this->setMensagem($recibo['mensagem']);
        } else {
            $this->setMensagem(null);
        }
        if (isset($recibo['modelo'])) {
            $this->setModelo($recibo['modelo']);
        } else {
            $this->setModelo(null);
        }
        return $this;
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_RETORNO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setConteudo($dom);
        $resp = $envio->envia();
        $this->loadNode($resp);
        if (!$this->isProcessado()) {
            return $this;
        }
        $protocolo = new Protocolo();
        $protocolo->loadNode($resp);
        return $protocolo;
    }

    public function consulta($nota = null)
    {
        if (!is_null($nota)) {
            $this->setAmbiente($nota->getAmbiente());
            $this->setModelo($nota->getModelo());
        }
        $dom = $this->getNode()->ownerDocument;
        $dom = $this->validar($dom);
        $retorno = $this->envia($dom);
        if ($retorno->isAutorizado() && !is_null($nota)) {
            $nota->setProtocolo($retorno);
        }
        return $retorno;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'consReciNFe':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $element->appendChild($versao);

        Util::appendNode($element, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($element, 'nRec', $this->getNumero(true));
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'retConsReciNFe':$name;
        if ($name == self::INFO_TAGNAME) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        } else {
            $element = parent::loadNode($element, $name);
        }
        $this->setNumero(
            Util::loadNode(
                $element,
                'nRec',
                'Tag "nRec" do campo "Numero" não encontrada'
            )
        );
        $this->setTempoMedio(Util::loadNode($element, 'tMed'));
        $this->setCodigo(Util::loadNode($element, 'cMsg'));
        $this->setMensagem(Util::loadNode($element, 'xMsg'));
        return $element;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(__DIR__) . '/Core/schema';
        $xsd_file = $xsd_path . '/consReciNFe_v3.10.xsd';
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
