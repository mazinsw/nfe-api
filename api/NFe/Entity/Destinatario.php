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
namespace NFe\Entity;

use NFe\Common\Util;

/**
 * Cliente pessoa física ou jurídica que está comprando os produtos e irá
 * receber a nota fiscal
 */
class Destinatario extends Pessoa
{
    
    /**
     * Indicador da IE do destinatário:
     * 1 – Contribuinte ICMSpagamento à
     * vista;
     * 2 – Contribuinte isento de inscrição;
     * 9 – Não Contribuinte
     */
    const INDICADOR_PAGAMENTO = 'pagamento';
    const INDICADOR_ISENTO = 'isento';
    const INDICADOR_NENHUM = 'nenhum';

    private $cpf;
    private $email;
    private $indicador;

    public function __construct($destinatario = array())
    {
        parent::__construct($destinatario);
    }

    /**
     * Número identificador do destinatario
     */
    public function getID($normalize = false)
    {
        if (!is_null($this->getCNPJ())) {
            return $this->getCNPJ($normalize);
        }
        return $this->getCPF($normalize);
    }

    /**
     * Nome do destinatário
     */
    public function getNome($normalize = false)
    {
        return $this->getRazaoSocial($normalize);
    }

    public function setNome($nome)
    {
        return $this->setRazaoSocial($nome);
    }

    /**
     * CPF do cliente
     */
    public function getCPF($normalize = false)
    {
        if (!$normalize) {
            return $this->cpf;
        }
        return $this->cpf;
    }

    public function setCPF($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * Informar o e-mail do destinatário. O campo pode ser utilizado para
     * informar o e-mail de recepção da NF-e indicada pelo destinatário
     */
    public function getEmail($normalize = false)
    {
        if (!$normalize) {
            return $this->email;
        }
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Indicador da IE do destinatário:
     * 1 – Contribuinte ICMSpagamento à
     * vista;
     * 2 – Contribuinte isento de inscrição;
     * 9 – Não Contribuinte
     */
    public function getIndicador($normalize = false)
    {
        if (!$normalize) {
            return $this->indicador;
        }
        switch ($this->indicador) {
            case self::INDICADOR_PAGAMENTO:
                return '1';
            case self::INDICADOR_ISENTO:
                return '2';
            case self::INDICADOR_NENHUM:
                return '9';
        }
        return $this->indicador;
    }

    public function setIndicador($indicador)
    {
        $this->indicador = $indicador;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $destinatario = parent::toArray($recursive);
        $destinatario['nome'] = $this->getNome();
        $destinatario['cpf'] = $this->getCPF();
        $destinatario['email'] = $this->getEmail();
        $destinatario['indicador'] = $this->getIndicador();
        return $destinatario;
    }

    public function fromArray($destinatario = array())
    {
        if ($destinatario instanceof Destinatario) {
            $destinatario = $destinatario->toArray();
        } elseif (!is_array($destinatario)) {
            return $this;
        }
        parent::fromArray($destinatario);
        if (isset($destinatario['nome'])) {
            $this->setNome($destinatario['nome']);
        } else {
            $this->setNome(null);
        }
        if (isset($destinatario['cpf'])) {
            $this->setCPF($destinatario['cpf']);
        } else {
            $this->setCPF(null);
        }
        if (isset($destinatario['email'])) {
            $this->setEmail($destinatario['email']);
        } else {
            $this->setEmail(null);
        }
        if (!isset($destinatario['indicador']) || is_null($destinatario['indicador'])) {
            $this->setIndicador(self::INDICADOR_NENHUM);
        } else {
            $this->setIndicador($destinatario['indicador']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'dest':$name);
        if (!is_null($this->getCNPJ())) {
            Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        } else {
            Util::appendNode($element, 'CPF', $this->getCPF(true));
        }
        if (!is_null($this->getNome())) {
            Util::appendNode($element, 'xNome', $this->getNome(true));
        }
        if (!is_null($this->getEndereco())) {
            $endereco = $this->getEndereco()->getNode('enderDest');
            $endereco = $dom->importNode($endereco, true);
            if (!is_null($this->getTelefone())) {
                Util::appendNode($endereco, 'fone', $this->getTelefone(true));
            }
            $element->appendChild($endereco);
        }
        Util::appendNode($element, 'indIEDest', $this->getIndicador(true));
        if (!is_null($this->getCNPJ()) && !is_null($this->getIE())) {
            Util::appendNode($element, 'IE', $this->getIE(true));
        }
        if (!is_null($this->getEmail())) {
            Util::appendNode($element, 'email', $this->getEmail(true));
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'dest':$name;
        $element = parent::loadNode($element, $name);
        $cpf = Util::loadNode($element, 'CPF');
        if (is_null($cpf) && is_null($this->getCNPJ())) {
            throw new \Exception('Tag "CPF" não encontrada no Destinatario', 404);
        }
        $this->setCPF($cpf);
        $this->setEmail(Util::loadNode($element, 'email'));
        $this->setIndicador(
            Util::loadNode(
                $element,
                'indIEDest',
                'Tag "indIEDest" não encontrada no Destinatario'
            )
        );
        return $element;
    }
}
