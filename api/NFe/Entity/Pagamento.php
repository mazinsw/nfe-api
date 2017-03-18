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

use NFe\Common\Node;
use NFe\Common\Util;

class Pagamento implements Node
{

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;99 - Outros
     */
    const FORMA_DINHEIRO = 'dinheiro';
    const FORMA_CHEQUE = 'cheque';
    const FORMA_CREDITO = 'credito';
    const FORMA_DEBITO = 'debito';
    const FORMA_CREDIARIO = 'crediario';
    const FORMA_ALIMENTACAO = 'alimentacao';
    const FORMA_REFEICAO = 'refeicao';
    const FORMA_PRESENTE = 'presente';
    const FORMA_COMBUSTIVEL = 'combustivel';
    const FORMA_OUTROS = 'outros';

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred; 99–Outros
     */
    const BANDEIRA_VISA = 'visa';
    const BANDEIRA_MASTERCARD = 'mastercard';
    const BANDEIRA_AMEX = 'amex';
    const BANDEIRA_SOROCRED = 'sorocred';
    const BANDEIRA_OUTROS = 'outros';

    private $forma;
    private $valor;
    private $integrado;
    private $credenciadora;
    private $autorizacao;
    private $bandeira;

    public function __construct($pagamento = array())
    {
        $this->fromArray($pagamento);
    }

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;99 - Outros
     */
    public function getForma($normalize = false)
    {
        if (!$normalize) {
            return $this->forma;
        }
        switch ($this->forma) {
            case self::FORMA_DINHEIRO:
                return '01';
            case self::FORMA_CHEQUE:
                return '02';
            case self::FORMA_CREDITO:
                return '03';
            case self::FORMA_DEBITO:
                return '04';
            case self::FORMA_CREDIARIO:
                return '05';
            case self::FORMA_ALIMENTACAO:
                return '10';
            case self::FORMA_REFEICAO:
                return '11';
            case self::FORMA_PRESENTE:
                return '12';
            case self::FORMA_COMBUSTIVEL:
                return '13';
            case self::FORMA_OUTROS:
                return '99';
        }
        return $this->forma;
    }

    public function setForma($forma)
    {
        switch ($forma) {
            case '01':
                $forma = self::FORMA_DINHEIRO;
                break;
            case '02':
                $forma = self::FORMA_CHEQUE;
                break;
            case '03':
                $forma = self::FORMA_CREDITO;
                break;
            case '04':
                $forma = self::FORMA_DEBITO;
                break;
            case '05':
                $forma = self::FORMA_CREDIARIO;
                break;
            case '10':
                $forma = self::FORMA_ALIMENTACAO;
                break;
            case '11':
                $forma = self::FORMA_REFEICAO;
                break;
            case '12':
                $forma = self::FORMA_PRESENTE;
                break;
            case '13':
                $forma = self::FORMA_COMBUSTIVEL;
                break;
            case '99':
                $forma = self::FORMA_OUTROS;
                break;
        }
        $this->forma = $forma;
        return $this;
    }

    /**
     * Valor do Pagamento
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->valor;
        }
        return Util::toCurrency($this->valor);
    }

    public function setValor($valor)
    {
        $valor = floatval($valor);
        $this->valor = $valor;
        return $this;
    }

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     */
    public function getIntegrado($normalize = false)
    {
        if (!$normalize) {
            return $this->integrado;
        }
        return $this->isIntegrado()?'1':'2';
    }

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     */
    public function isIntegrado()
    {
        return $this->integrado == 'Y';
    }

    public function setIntegrado($integrado)
    {
        if (!in_array($integrado, array('N', 'Y'))) {
            $integrado = $integrado == '1'?'Y':'N';
        }
        $this->integrado = $integrado;
        return $this;
    }

    /**
     * CNPJ da credenciadora de cartão de crédito/débito
     */
    public function getCredenciadora($normalize = false)
    {
        if (!$normalize) {
            return $this->credenciadora;
        }
        return $this->credenciadora;
    }

    public function setCredenciadora($credenciadora)
    {
        $this->credenciadora = $credenciadora;
        return $this;
    }

    /**
     * Número de autorização da operação cartão de crédito/débito
     */
    public function getAutorizacao($normalize = false)
    {
        if (!$normalize) {
            return $this->autorizacao;
        }
        return $this->autorizacao;
    }

    public function setAutorizacao($autorizacao)
    {
        $this->autorizacao = $autorizacao;
        return $this;
    }

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred; 99–Outros
     */
    public function getBandeira($normalize = false)
    {
        if (!$normalize) {
            return $this->bandeira;
        }
        switch ($this->bandeira) {
            case self::BANDEIRA_VISA:
                return '01';
            case self::BANDEIRA_MASTERCARD:
                return '02';
            case self::BANDEIRA_AMEX:
                return '03';
            case self::BANDEIRA_SOROCRED:
                return '04';
            case self::BANDEIRA_OUTROS:
                return '99';
        }
        return $this->bandeira;
    }

    public function setBandeira($bandeira)
    {
        switch ($bandeira) {
            case '01':
                $bandeira = self::BANDEIRA_VISA;
                break;
            case '02':
                $bandeira = self::BANDEIRA_MASTERCARD;
                break;
            case '03':
                $bandeira = self::BANDEIRA_AMEX;
                break;
            case '04':
                $bandeira = self::BANDEIRA_SOROCRED;
                break;
            case '99':
                $bandeira = self::BANDEIRA_OUTROS;
                break;
        }
        $this->bandeira = $bandeira;
        return $this;
    }

    /**
     * Informa se o pagamento é em cartão
     */
    public function isCartao()
    {
        return in_array($this->getForma(), array(self::FORMA_CREDITO, self::FORMA_DEBITO));
    }

    public function toArray($recursive = false)
    {
        $pagamento = array();
        $pagamento['forma'] = $this->getForma();
        $pagamento['valor'] = $this->getValor();
        $pagamento['integrado'] = $this->getIntegrado();
        $pagamento['credenciadora'] = $this->getCredenciadora();
        $pagamento['autorizacao'] = $this->getAutorizacao();
        $pagamento['bandeira'] = $this->getBandeira();
        return $pagamento;
    }

    public function fromArray($pagamento = array())
    {
        if ($pagamento instanceof Pagamento) {
            $pagamento = $pagamento->toArray();
        } elseif (!is_array($pagamento)) {
            return $this;
        }
        if (isset($pagamento['forma'])) {
            $this->setForma($pagamento['forma']);
        } else {
            $this->setForma(null);
        }
        if (isset($pagamento['valor'])) {
            $this->setValor($pagamento['valor']);
        } else {
            $this->setValor(null);
        }
        if (!isset($pagamento['integrado']) || is_null($pagamento['integrado'])) {
            $this->setIntegrado('N');
        } else {
            $this->setIntegrado($pagamento['integrado']);
        }
        if (isset($pagamento['credenciadora'])) {
            $this->setCredenciadora($pagamento['credenciadora']);
        } else {
            $this->setCredenciadora(null);
        }
        if (isset($pagamento['autorizacao'])) {
            $this->setAutorizacao($pagamento['autorizacao']);
        } else {
            $this->setAutorizacao(null);
        }
        if (isset($pagamento['bandeira'])) {
            $this->setBandeira($pagamento['bandeira']);
        } else {
            $this->setBandeira(null);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'pag':$name);
        Util::appendNode($element, 'tPag', $this->getForma(true));
        Util::appendNode($element, 'vPag', $this->getValor(true));
        if (!$this->isCartao()) {
            return $element;
        }
        $cartao = $dom->createElement('card');
        Util::appendNode($cartao, 'tpIntegra', $this->getIntegrado(true));
        if ($this->isIntegrado()) {
            Util::appendNode($cartao, 'CNPJ', $this->getCredenciadora(true));
        }
        Util::appendNode($cartao, 'tBand', $this->getBandeira(true));
        if ($this->isIntegrado()) {
            Util::appendNode($cartao, 'cAut', $this->getAutorizacao(true));
        }
        $element->appendChild($cartao);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'pag':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setForma(
            Util::loadNode(
                $element,
                'tPag',
                'Tag "tPag" do campo "Forma" não encontrada'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vPag',
                'Tag "vPag" do campo "Valor" não encontrada'
            )
        );
        $integrado = Util::loadNode($element, 'tpIntegra');
        if (is_null($integrado) && $this->isCartao()) {
            throw new \Exception('Tag "tpIntegra" do campo "Integrado" não encontrada', 404);
        }
        $this->setIntegrado($integrado);
        $this->setCredenciadora(Util::loadNode($element, 'CNPJ'));
        $autorizacao = Util::loadNode($element, 'cAut');
        if (is_null($autorizacao) && $this->isCartao() && is_numeric($this->getCredenciadora())) {
            throw new \Exception('Tag "cAut" do campo "Autorizacao" não encontrada', 404);
        }
        $this->setAutorizacao($autorizacao);
        $bandeira = Util::loadNode($element, 'tBand');
        if (is_null($bandeira) && $this->isCartao() && is_numeric($this->getCredenciadora())) {
            throw new \Exception('Tag "tBand" do campo "Bandeira" não encontrada', 404);
        }
        $this->setBandeira($bandeira);
        return $element;
    }
}
