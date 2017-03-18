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

/**
 * Protocolo de autorização da nota, é retornado pela autorização, recibo
 * ou situação e anexado à nota
 */
class Protocolo extends Retorno
{

    private $chave;
    private $validacao;
    private $numero;

    public function __construct($protocolo = array())
    {
        parent::__construct($protocolo);
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
     * Digest Value da NF-e processada. Utilizado para conferir a integridade
     * da NF-e original.
     */
    public function getValidacao($normalize = false)
    {
        if (!$normalize) {
            return $this->validacao;
        }
        return $this->validacao;
    }

    public function setValidacao($validacao)
    {
        $this->validacao = $validacao;
        return $this;
    }

    /**
     * Número do Protocolo de Status da NF-e. 1 posição (1 – Secretaria de
     * Fazenda Estadual 2 – Receita Federal); 2 - códiga da UF - 2 posições
     * ano; 10 seqüencial no ano.
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

    public function toArray($recursive = false)
    {
        $protocolo = parent::toArray($recursive);
        $protocolo['chave'] = $this->getChave();
        $protocolo['validacao'] = $this->getValidacao();
        $protocolo['numero'] = $this->getNumero();
        return $protocolo;
    }

    public function fromArray($protocolo = array())
    {
        if ($protocolo instanceof Protocolo) {
            $protocolo = $protocolo->toArray();
        } elseif (!is_array($protocolo)) {
            return $this;
        }
        parent::fromArray($protocolo);
        if (isset($protocolo['chave'])) {
            $this->setChave($protocolo['chave']);
        } else {
            $this->setChave(null);
        }
        if (isset($protocolo['validacao'])) {
            $this->setValidacao($protocolo['validacao']);
        } else {
            $this->setValidacao(null);
        }
        if (isset($protocolo['numero'])) {
            $this->setNumero($protocolo['numero']);
        } else {
            $this->setNumero(null);
        }
        return $this;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'infProt':$name;
        $element = parent::loadNode($element, $name);
        $this->setChave(
            Util::loadNode(
                $element,
                'chNFe',
                'Tag "chNFe" não encontrada no Protocolo'
            )
        );
        $this->setValidacao(Util::loadNode($element, 'digVal'));
        $this->setNumero(Util::loadNode($element, 'nProt'));
        return $element;
    }

    public function getNode($name = null)
    {
        $old_uf = $this->getUF();
        $this->setUF(null);
        $info = parent::getNode('infProt');
        $this->setUF($old_uf);
        $dom = $info->ownerDocument;
        $element = $dom->createElement(is_null($name)?'protNFe':$name);
        $versao = $dom->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $element->appendChild($versao);

        $id = $dom->createAttribute('Id');
        $id->value = 'ID'.$this->getNumero(true);
        $info->appendChild($id);

        $status = $info->getElementsByTagName('cStat')->item(0);
        Util::appendNode($info, 'nProt', $this->getNumero(true), $status);
        Util::appendNode($info, 'digVal', $this->getValidacao(true), $status);
        $nodes = $info->getElementsByTagName('dhRecbto');
        if ($nodes->length > 0) {
            $recebimento = $nodes->item(0);
        } else {
            $recebimento = $status;
        }
        Util::appendNode($info, 'chNFe', $this->getChave(true), $recebimento);
        $element->appendChild($info);
        return $element;
    }
}
