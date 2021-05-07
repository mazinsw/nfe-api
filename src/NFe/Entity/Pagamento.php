<?php

/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
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
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     */
    public const INDICADOR_AVISTA = 'avista';
    public const INDICADOR_APRAZO = 'aprazo';

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     */
    public const FORMA_DINHEIRO = 'dinheiro';
    public const FORMA_CHEQUE = 'cheque';
    public const FORMA_CREDITO = 'credito';
    public const FORMA_DEBITO = 'debito';
    public const FORMA_CREDIARIO = 'crediario';
    public const FORMA_ALIMENTACAO = 'alimentacao';
    public const FORMA_REFEICAO = 'refeicao';
    public const FORMA_PRESENTE = 'presente';
    public const FORMA_COMBUSTIVEL = 'combustivel';
    public const FORMA_DUPLICATA = 'duplicata';
    public const FORMA_BOLETO = 'boleto';
    public const FORMA_DEPOSITO = 'deposito';
    public const FORMA_INSTANTANEO = 'instantaneo';
    public const FORMA_TRANSFERENCIA = 'transferencia';
    public const FORMA_FIDELIDADE = 'fidelidade';
    public const FORMA_CORTESIA = 'cortesia';
    public const FORMA_OUTROS = 'outros';

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     */
    public const BANDEIRA_VISA = 'visa';
    public const BANDEIRA_MASTERCARD = 'mastercard';
    public const BANDEIRA_AMEX = 'amex';
    public const BANDEIRA_SOROCRED = 'sorocred';
    public const BANDEIRA_DINERS = 'diners';
    public const BANDEIRA_ELO = 'elo';
    public const BANDEIRA_HIPERCARD = 'hipercard';
    public const BANDEIRA_AURA = 'aura';
    public const BANDEIRA_CABAL = 'cabal';
    public const BANDEIRA_OUTROS = 'outros';

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     */
    private $indicador;

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     *
     * @var string
     */
    private $forma;

    /**
     * Valor do Pagamento
     *
     * @var float
     */
    private $valor;

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     *
     * @var string
     */
    private $integrado;

    /**
     * CNPJ da credenciadora de cartão de crédito/débito
     *
     * @var string
     */
    private $credenciadora;

    /**
     * Número de autorização da operação cartão de crédito/débito
     *
     * @var string
     */
    private $autorizacao;

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     *
     * @var string
     */
    private $bandeira;

    /**
     * Constroi uma instância de Pagamento vazia
     * @param array $pagamento Array contendo dados do Pagamento
     */
    public function __construct($pagamento = [])
    {
        $this->fromArray($pagamento);
    }

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     * @param boolean $normalize informa se o indicador deve estar no formato do XML
     * @return mixed indicador da Nota
     */
    public function getIndicador($normalize = false)
    {
        if (!$normalize) {
            return $this->indicador;
        }
        switch ($this->indicador) {
            case self::INDICADOR_AVISTA:
                return '0';
            case self::INDICADOR_APRAZO:
                return '1';
        }
        return $this->indicador;
    }

    /**
     * Altera o valor do Indicador para o informado no parâmetro
     * @param mixed $indicador novo valor para Indicador
     * @return Nota A própria instância da classe
     */
    public function setIndicador($indicador)
    {
        switch ($indicador) {
            case '0':
                $indicador = self::INDICADOR_AVISTA;
                break;
            case '1':
                $indicador = self::INDICADOR_APRAZO;
                break;
        }
        $this->indicador = $indicador;
        return $this;
    }

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     * @param boolean $normalize informa se o forma deve estar no formato do XML
     * @return string forma of Pagamento
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
            case self::FORMA_DUPLICATA:
                return '14';
            case self::FORMA_BOLETO:
                return '15';
            case self::FORMA_DEPOSITO:
                return '16';
            case self::FORMA_INSTANTANEO:
                return '17';
            case self::FORMA_TRANSFERENCIA:
                return '18';
            case self::FORMA_FIDELIDADE:
                return '19';
            case self::FORMA_CORTESIA:
                return '90';
            case self::FORMA_OUTROS:
                return '99';
        }
        return $this->forma;
    }

    /**
     * Altera o valor do Forma para o informado no parâmetro
     * @param mixed $forma novo valor para Forma
     * @param string $forma Novo forma para Pagamento
     * @return self A própria instância da classe
     */
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
            case '14':
                $forma = self::FORMA_DUPLICATA;
                break;
            case '15':
                $forma = self::FORMA_BOLETO;
                break;
            case '16':
                $forma = self::FORMA_DEPOSITO;
                break;
            case '17':
                $forma = self::FORMA_INSTANTANEO;
                break;
            case '18':
                $forma = self::FORMA_TRANSFERENCIA;
                break;
            case '19':
                $forma = self::FORMA_FIDELIDADE;
                break;
            case '90':
                $forma = self::FORMA_CORTESIA;
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
     * @param boolean $normalize informa se a valor deve estar no formato do XML
     * @return float valor of Pagamento
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->valor;
        }
        return Util::toCurrency($this->valor);
    }

    /**
     * Altera o valor da Valor para o informado no parâmetro
     * @param mixed $valor novo valor para Valor
     * @param float $valor Novo valor para Pagamento
     * @return self A própria instância da classe
     */
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
     * @param boolean $normalize informa se o integrado deve estar no formato do XML
     * @return string integrado of Pagamento
     */
    public function getIntegrado($normalize = false)
    {
        if (!$normalize) {
            return $this->integrado;
        }
        return $this->isIntegrado() ? '1' : '2';
    }

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     * @return boolean informa se o Integrado está habilitado
     */
    public function isIntegrado()
    {
        return $this->integrado == 'Y';
    }

    /**
     * Altera o valor do Integrado para o informado no parâmetro
     * @param mixed $integrado novo valor para Integrado
     * @param string $integrado Novo integrado para Pagamento
     * @return self A própria instância da classe
     */
    public function setIntegrado($integrado)
    {
        if (is_bool($integrado)) {
            $integrado = $integrado ? 'Y' : 'N';
        }
        $this->integrado = in_array($integrado, ['Y', '1']) ? 'Y' : 'N';
        return $this;
    }

    /**
     * CNPJ da credenciadora de cartão de crédito/débito
     * @param boolean $normalize informa se a credenciadora deve estar no formato do XML
     * @return string credenciadora of Pagamento
     */
    public function getCredenciadora($normalize = false)
    {
        if (!$normalize) {
            return $this->credenciadora;
        }
        return $this->credenciadora;
    }

    /**
     * Altera o valor da Credenciadora para o informado no parâmetro
     * @param mixed $credenciadora novo valor para Credenciadora
     * @param string $credenciadora Novo credenciadora para Pagamento
     * @return self A própria instância da classe
     */
    public function setCredenciadora($credenciadora)
    {
        $this->credenciadora = $credenciadora;
        return $this;
    }

    /**
     * Número de autorização da operação cartão de crédito/débito
     * @param boolean $normalize informa se a autorizacao deve estar no formato do XML
     * @return string autorizacao of Pagamento
     */
    public function getAutorizacao($normalize = false)
    {
        if (!$normalize) {
            return $this->autorizacao;
        }
        return $this->autorizacao;
    }

    /**
     * Altera o valor da Autorizacao para o informado no parâmetro
     * @param mixed $autorizacao novo valor para Autorizacao
     * @param string $autorizacao Novo autorizacao para Pagamento
     * @return self A própria instância da classe
     */
    public function setAutorizacao($autorizacao)
    {
        $this->autorizacao = $autorizacao;
        return $this;
    }

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     * @param boolean $normalize informa se a bandeira deve estar no formato do XML
     * @return string bandeira of Pagamento
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
            case self::BANDEIRA_DINERS:
                return '05';
            case self::BANDEIRA_ELO:
                return '06';
            case self::BANDEIRA_HIPERCARD:
                return '07';
            case self::BANDEIRA_AURA:
                return '08';
            case self::BANDEIRA_CABAL:
                return '09';
            case self::BANDEIRA_OUTROS:
                return '99';
        }
        return $this->bandeira;
    }

    /**
     * Altera o valor da Bandeira para o informado no parâmetro
     * @param mixed $bandeira novo valor para Bandeira
     * @param string $bandeira Novo bandeira para Pagamento
     * @return self A própria instância da classe
     */
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
            case '05':
                $bandeira = self::BANDEIRA_DINERS;
                break;
            case '06':
                $bandeira = self::BANDEIRA_ELO;
                break;
            case '07':
                $bandeira = self::BANDEIRA_HIPERCARD;
                break;
            case '08':
                $bandeira = self::BANDEIRA_AURA;
                break;
            case '09':
                $bandeira = self::BANDEIRA_CABAL;
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
        return in_array($this->getForma(), [self::FORMA_CREDITO, self::FORMA_DEBITO]);
    }

    public function toArray($recursive = false)
    {
        $pagamento = [];
        $pagamento['indicador'] = $this->getIndicador();
        $pagamento['forma'] = $this->getForma();
        $pagamento['valor'] = $this->getValor();
        $pagamento['integrado'] = $this->getIntegrado();
        $pagamento['credenciadora'] = $this->getCredenciadora();
        $pagamento['autorizacao'] = $this->getAutorizacao();
        $pagamento['bandeira'] = $this->getBandeira();
        return $pagamento;
    }

    public function fromArray($pagamento = [])
    {
        if ($pagamento instanceof Pagamento) {
            $pagamento = $pagamento->toArray();
        } elseif (!is_array($pagamento)) {
            return $this;
        }
        if (isset($pagamento['indicador'])) {
            $this->setIndicador($pagamento['indicador']);
        } else {
            $this->setIndicador(null);
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
        if (!isset($pagamento['integrado'])) {
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
        if ($this->getValor() < 0) {
            $element = $dom->createElement(is_null($name) ? 'vTroco' : $name);
            $this->setValor(-$this->getValor());
            $element->appendChild($dom->createTextNode($this->getValor(true)));
            $this->setValor(-$this->getValor());
            return $element;
        }
        $element = $dom->createElement(is_null($name) ? 'detPag' : $name);
        if (!is_null($this->getIndicador())) {
            Util::appendNode($element, 'indPag', $this->getIndicador(true));
        }
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
        if (!is_null($this->getBandeira())) {
            Util::appendNode($cartao, 'tBand', $this->getBandeira(true));
        }
        if ($this->isIntegrado()) {
            Util::appendNode($cartao, 'cAut', $this->getAutorizacao(true));
        }
        $element->appendChild($cartao);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'detPag' : $name;
        if ($name == 'vTroco') {
            $this->setValor(
                '-' . Util::loadNode(
                    $element,
                    'vTroco',
                    'Tag "vTroco" do campo "Valor" não encontrada'
                )
            );
            return $element;
        }
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setIndicador(
            Util::loadNode(
                $element,
                'indPag'
            )
        );
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
