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
use FR3D\XmlDSig\Adapter\AdapterInterface;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;

/**
 * Classe base para a formação da nota fiscal
 */
abstract class NF implements NodeInterface
{

    const VERSAO = '3.10';
    const APP_VERSAO = '1.0';
    const PORTAL = 'http://www.portalfiscal.inf.br/nfe';

    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     */
    const TIPO_ENTRADA = 'entrada';
    const TIPO_SAIDA = 'saida';

    /**
     * Identificador de Local de destino da operação
     * (1-Interna;2-Interestadual;3-Exterior)
     */
    const DESTINO_INTERNA = 'interna';
    const DESTINO_INTERESTADUAL = 'interestadual';
    const DESTINO_EXTERIOR = 'exterior';

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo; 2 – outros.
     */
    const INDICADOR_AVISTA = 'avista';
    const INDICADOR_APRAZO = 'aprazo';
    const INDICADOR_OUTROS = 'outros';

    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     */
    const FORMATO_NENHUMA = 'nenhuma';
    const FORMATO_RETRATO = 'retrato';
    const FORMATO_PAISAGEM = 'paisagem';
    const FORMATO_SIMPLIFICADO = 'simplificado';
    const FORMATO_CONSUMIDOR = 'consumidor';
    const FORMATO_MENSAGEM = 'mensagem';

    /**
     * Forma de emissão da NF-e
     */
    const EMISSAO_NORMAL = 'normal';
    const EMISSAO_CONTINGENCIA = 'contingencia';

    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     */
    const AMBIENTE_PRODUCAO = 'producao';
    const AMBIENTE_HOMOLOGACAO = 'homologacao';

    /**
     * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
     * NFe de ajuste, 4 - Devolução/Retorno
     */
    const FINALIDADE_NORMAL = 'normal';
    const FINALIDADE_COMPLEMENTAR = 'complementar';
    const FINALIDADE_AJUSTE = 'ajuste';
    const FINALIDADE_RETORNO = 'retorno';

    /**
     * Indicador de presença do comprador no estabelecimento comercial no
     * momento da oepração (0-Não se aplica, ex.: Nota Fiscal complementar ou
     * de ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
     * presencial, teleatendimento;4-NFC-e entrega em domicílio;9-Não
     * presencial, outros)
     */
    const PRESENCA_NENHUM = 'nenhum';
    const PRESENCA_PRESENCIAL = 'presencial';
    const PRESENCA_INTERNET = 'internet';
    const PRESENCA_TELEATENDIMENTO = 'teleatendimento';
    const PRESENCA_ENTREGA = 'entrega';
    const PRESENCA_OUTROS = 'outros';

    private $id;
    private $numero;
    private $emitente;
    private $cliente;
    private $produtos;
    private $transporte;
    private $pagamentos;
    private $data_movimentacao;
    private $data_contingencia;
    private $justificativa;
    private $modelo;
    private $tipo;
    private $destino;
    private $natureza;
    private $codigo;
    private $indicador;
    private $data_emissao;
    private $serie;
    private $formato;
    private $emissao;
    private $digito_verificador;
    private $ambiente;
    private $finalidade;
    private $consumidor_final;
    private $presenca;
    private $protocolo;

    public function __construct($nf = array())
    {
        $this->fromArray($nf);
    }

    public function getID($normalize = false)
    {
        if (!$normalize) {
            return $this->id;
        }
        return 'NFe'.$this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Número do Documento Fiscal
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

    public function getEmitente()
    {
        return $this->emitente;
    }

    public function setEmitente($emitente)
    {
        $this->emitente = $emitente;
        return $this;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
        return $this;
    }

    public function getProdutos()
    {
        return $this->produtos;
    }

    public function setProdutos($produtos)
    {
        $this->produtos = $produtos;
        return $this;
    }

    public function addProduto($produto)
    {
        $this->produtos[] = $produto;
        return $this;
    }

    public function getTransporte()
    {
        return $this->transporte;
    }

    public function setTransporte($transporte)
    {
        $this->transporte = $transporte;
        return $this;
    }

    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    public function setPagamentos($pagamentos)
    {
        $this->pagamentos = $pagamentos;
        return $this;
    }

    public function addPagamento($pagamento)
    {
        $this->pagamentos[] = $pagamento;
        return $this;
    }

    /**
     * Data e Hora da saída ou de entrada da mercadoria / produto
     */
    public function getDataMovimentacao($normalize = false)
    {
        if (!$normalize) {
            return $this->data_movimentacao;
        }
        return Util::toDateTime($this->data_movimentacao);
    }

    public function setDataMovimentacao($data_movimentacao)
    {
        if (!is_null($data_movimentacao) && !is_numeric($data_movimentacao)) {
            $data_movimentacao = strtotime($data_movimentacao);
        }
        $this->data_movimentacao = $data_movimentacao;
        return $this;
    }

    /**
     * Informar a data e hora de entrada em contingência
     */
    public function getDataContingencia($normalize = false)
    {
        if (!$normalize) {
            return $this->data_contingencia;
        }
        return Util::toDateTime($this->data_contingencia);
    }

    public function setDataContingencia($data_contingencia)
    {
        if (!is_null($data_contingencia) && !is_numeric($data_contingencia)) {
            $data_contingencia = strtotime($data_contingencia);
        }
        $this->data_contingencia = $data_contingencia;
        return $this;
    }

    /**
     * Informar a Justificativa da entrada em contingência
     */
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

    public function getModelo($normalize = false)
    {
        if (!$normalize) {
            return $this->modelo;
        }
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $modelo = intval($modelo);
        $this->modelo = $modelo;
        return $this;
    }

    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     */
    public function getTipo($normalize = false)
    {
        if (!$normalize) {
            return $this->tipo;
        }
        switch ($this->tipo) {
            case self::TIPO_ENTRADA:
                return '0';
            case self::TIPO_SAIDA:
                return '1';
        }
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        switch ($tipo) {
            case '0':
                $tipo = self::TIPO_ENTRADA;
                break;
            case '1':
                $tipo = self::TIPO_SAIDA;
                break;
        }
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Identificador de Local de destino da operação
     * (1-Interna;2-Interestadual;3-Exterior)
     */
    public function getDestino($normalize = false)
    {
        if (!$normalize) {
            return $this->destino;
        }
        switch ($this->destino) {
            case self::DESTINO_INTERNA:
                return '1';
            case self::DESTINO_INTERESTADUAL:
                return '2';
            case self::DESTINO_EXTERIOR:
                return '3';
        }
        return $this->destino;
    }

    public function setDestino($destino)
    {
        switch ($destino) {
            case '1':
                $destino = self::DESTINO_INTERNA;
                break;
            case '2':
                $destino = self::DESTINO_INTERESTADUAL;
                break;
            case '3':
                $destino = self::DESTINO_EXTERIOR;
                break;
        }
        $this->destino = $destino;
        return $this;
    }

    /**
     * Descrição da Natureza da Operação
     */
    public function getNatureza($normalize = false)
    {
        if (!$normalize) {
            return $this->natureza;
        }
        return $this->natureza;
    }

    public function setNatureza($natureza)
    {
        $this->natureza = $natureza;
        return $this;
    }

    /**
     * Código numérico que compõe a Chave de Acesso. Número aleatório gerado
     * pelo emitente para cada NF-e.
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return Util::padDigit($this->codigo % 100000000, 8);
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo; 2 – outros.
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
            case self::INDICADOR_OUTROS:
                return '2';
        }
        return $this->indicador;
    }

    public function setIndicador($indicador)
    {
        switch ($indicador) {
            case '0':
                $indicador = self::INDICADOR_AVISTA;
                break;
            case '1':
                $indicador = self::INDICADOR_APRAZO;
                break;
            case '2':
                $indicador = self::INDICADOR_OUTROS;
                break;
        }
        $this->indicador = $indicador;
        return $this;
    }

    /**
     * Data e Hora de emissão do Documento Fiscal
     */
    public function getDataEmissao($normalize = false)
    {
        if (!$normalize) {
            return $this->data_emissao;
        }
        return Util::toDateTime($this->data_emissao);
    }

    public function setDataEmissao($data_emissao)
    {
        if (!is_numeric($data_emissao)) {
            $data_emissao = strtotime($data_emissao);
        }
        $this->data_emissao = $data_emissao;
        return $this;
    }

    /**
     * Série do Documento Fiscal: série normal 0-889, Avulsa Fisco 890-899,
     * SCAN 900-999
     */
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

    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     */
    public function getFormato($normalize = false)
    {
        if (!$normalize) {
            return $this->formato;
        }
        switch ($this->formato) {
            case self::FORMATO_NENHUMA:
                return '0';
            case self::FORMATO_RETRATO:
                return '1';
            case self::FORMATO_PAISAGEM:
                return '2';
            case self::FORMATO_SIMPLIFICADO:
                return '3';
            case self::FORMATO_CONSUMIDOR:
                return '4';
            case self::FORMATO_MENSAGEM:
                return '5';
        }
        return $this->formato;
    }

    public function setFormato($formato)
    {
        switch ($formato) {
            case '0':
                $formato = self::FORMATO_NENHUMA;
                break;
            case '1':
                $formato = self::FORMATO_RETRATO;
                break;
            case '2':
                $formato = self::FORMATO_PAISAGEM;
                break;
            case '3':
                $formato = self::FORMATO_SIMPLIFICADO;
                break;
            case '4':
                $formato = self::FORMATO_CONSUMIDOR;
                break;
            case '5':
                $formato = self::FORMATO_MENSAGEM;
                break;
        }
        $this->formato = $formato;
        return $this;
    }

    /**
     * Forma de emissão da NF-e
     */
    public function getEmissao($normalize = false)
    {
        if (!$normalize) {
            return $this->emissao;
        }
        switch ($this->emissao) {
            case self::EMISSAO_NORMAL:
                return '1';
            case self::EMISSAO_CONTINGENCIA:
                return '9';
        }
        return $this->emissao;
    }

    public function setEmissao($emissao)
    {
        switch ($emissao) {
            case '1':
                $emissao = self::EMISSAO_NORMAL;
                break;
            case '9':
                $emissao = self::EMISSAO_CONTINGENCIA;
                break;
        }
        $this->emissao = $emissao;
        return $this;
    }

    /**
     * Digito Verificador da Chave de Acesso da NF-e
     */
    public function getDigitoVerificador($normalize = false)
    {
        if (!$normalize) {
            return $this->digito_verificador;
        }
        return $this->digito_verificador;
    }

    public function setDigitoVerificador($digito_verificador)
    {
        $this->digito_verificador = $digito_verificador;
        return $this;
    }

    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     */
    public function getAmbiente($normalize = false)
    {
        if (!$normalize) {
            return $this->ambiente;
        }
        switch ($this->ambiente) {
            case self::AMBIENTE_PRODUCAO:
                return '1';
            case self::AMBIENTE_HOMOLOGACAO:
                return '2';
        }
        return $this->ambiente;
    }

    public function setAmbiente($ambiente)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = self::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = self::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
     * NFe de ajuste, 4 - Devolução/Retorno
     */
    public function getFinalidade($normalize = false)
    {
        if (!$normalize) {
            return $this->finalidade;
        }
        switch ($this->finalidade) {
            case self::FINALIDADE_NORMAL:
                return '1';
            case self::FINALIDADE_COMPLEMENTAR:
                return '2';
            case self::FINALIDADE_AJUSTE:
                return '3';
            case self::FINALIDADE_RETORNO:
                return '4';
        }
        return $this->finalidade;
    }

    public function setFinalidade($finalidade)
    {
        switch ($finalidade) {
            case '1':
                $finalidade = self::FINALIDADE_NORMAL;
                break;
            case '2':
                $finalidade = self::FINALIDADE_COMPLEMENTAR;
                break;
            case '3':
                $finalidade = self::FINALIDADE_AJUSTE;
                break;
            case '4':
                $finalidade = self::FINALIDADE_RETORNO;
                break;
        }
        $this->finalidade = $finalidade;
        return $this;
    }

    /**
     * Indica operação com consumidor final (0-Não;1-Consumidor Final)
     */
    public function getConsumidorFinal($normalize = false)
    {
        if (!$normalize) {
            return $this->consumidor_final;
        }
        switch ($this->consumidor_final) {
            case 'N':
                return '0';
            case 'Y':
                return '1';
        }
        return $this->consumidor_final;
    }

    /**
     * Indica operação com consumidor final (0-Não;1-Consumidor Final)
     */
    public function isConsumidorFinal()
    {
        return $this->consumidor_final == 'Y';
    }

    public function setConsumidorFinal($consumidor_final)
    {
        if (!in_array($consumidor_final, array('N', 'Y'))) {
            $consumidor_final = $consumidor_final?'Y':'N';
        }
        $this->consumidor_final = $consumidor_final;
        return $this;
    }

    /**
     * Indicador de presença do comprador no estabelecimento comercial no
     * momento da oepração (0-Não se aplica, ex.: Nota Fiscal complementar ou
     * de ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
     * presencial, teleatendimento;4-NFC-e entrega em domicílio;9-Não
     * presencial, outros)
     */
    public function getPresenca($normalize = false)
    {
        if (!$normalize) {
            return $this->presenca;
        }
        switch ($this->presenca) {
            case self::PRESENCA_NENHUM:
                return '0';
            case self::PRESENCA_PRESENCIAL:
                return '1';
            case self::PRESENCA_INTERNET:
                return '2';
            case self::PRESENCA_TELEATENDIMENTO:
                return '3';
            case self::PRESENCA_ENTREGA:
                return '4';
            case self::PRESENCA_OUTROS:
                return '9';
        }
        return $this->presenca;
    }

    public function setPresenca($presenca)
    {
        switch ($presenca) {
            case '0':
                $presenca = self::PRESENCA_NENHUM;
                break;
            case '1':
                $presenca = self::PRESENCA_PRESENCIAL;
                break;
            case '2':
                $presenca = self::PRESENCA_INTERNET;
                break;
            case '3':
                $presenca = self::PRESENCA_TELEATENDIMENTO;
                break;
            case '4':
                $presenca = self::PRESENCA_ENTREGA;
                break;
            case '9':
                $presenca = self::PRESENCA_OUTROS;
                break;
        }
        $this->presenca = $presenca;
        return $this;
    }

    public function getProtocolo()
    {
        return $this->protocolo;
    }

    public function setProtocolo($protocolo)
    {
        $this->protocolo = $protocolo;
        return $this;
    }

    public function toArray()
    {
        $nf = array();
        $nf['id'] = $this->getID();
        $nf['numero'] = $this->getNumero();
        $nf['emitente'] = $this->getEmitente();
        $nf['cliente'] = $this->getCliente();
        $nf['produtos'] = $this->getProdutos();
        $nf['transporte'] = $this->getTransporte();
        $nf['pagamentos'] = $this->getPagamentos();
        $nf['data_movimentacao'] = $this->getDataMovimentacao();
        $nf['data_contingencia'] = $this->getDataContingencia();
        $nf['justificativa'] = $this->getJustificativa();
        $nf['modelo'] = $this->getModelo();
        $nf['tipo'] = $this->getTipo();
        $nf['destino'] = $this->getDestino();
        $nf['natureza'] = $this->getNatureza();
        $nf['codigo'] = $this->getCodigo();
        $nf['indicador'] = $this->getIndicador();
        $nf['data_emissao'] = $this->getDataEmissao();
        $nf['serie'] = $this->getSerie();
        $nf['formato'] = $this->getFormato();
        $nf['emissao'] = $this->getEmissao();
        $nf['digito_verificador'] = $this->getDigitoVerificador();
        $nf['ambiente'] = $this->getAmbiente();
        $nf['finalidade'] = $this->getFinalidade();
        $nf['consumidor_final'] = $this->getConsumidorFinal();
        $nf['presenca'] = $this->getPresenca();
        $nf['protocolo'] = $this->getProtocolo();
        return $nf;
    }

    public function fromArray($nf = array())
    {
        if ($nf instanceof NF) {
            $nf = $nf->toArray();
        } elseif (!is_array($nf)) {
            return $this;
        }
        if (isset($nf['id'])) {
            $this->setID($nf['id']);
        } else {
            $this->setID(null);
        }
        if (isset($nf['numero'])) {
            $this->setNumero($nf['numero']);
        } else {
            $this->setNumero(null);
        }
        if (!isset($nf['emitente']) || is_null($nf['emitente'])) {
            $this->setEmitente(new Emitente());
        } else {
            $this->setEmitente($nf['emitente']);
        }
        if (!isset($nf['cliente']) || is_null($nf['cliente'])) {
            $this->setCliente(new Cliente());
        } else {
            $this->setCliente($nf['cliente']);
        }
        if (!isset($nf['produtos']) || is_null($nf['produtos'])) {
            $this->setProdutos(array());
        } else {
            $this->setProdutos($nf['produtos']);
        }
        if (!isset($nf['transporte']) || is_null($nf['transporte'])) {
            $this->setTransporte(new Transporte());
        } else {
            $this->setTransporte($nf['transporte']);
        }
        if (!isset($nf['pagamentos']) || is_null($nf['pagamentos'])) {
            $this->setPagamentos(array());
        } else {
            $this->setPagamentos($nf['pagamentos']);
        }
        if (isset($nf['data_movimentacao'])) {
            $this->setDataMovimentacao($nf['data_movimentacao']);
        } else {
            $this->setDataMovimentacao(null);
        }
        if (isset($nf['data_contingencia'])) {
            $this->setDataContingencia($nf['data_contingencia']);
        } else {
            $this->setDataContingencia(null);
        }
        if (isset($nf['justificativa'])) {
            $this->setJustificativa($nf['justificativa']);
        } else {
            $this->setJustificativa(null);
        }
        if (isset($nf['modelo'])) {
            $this->setModelo($nf['modelo']);
        } else {
            $this->setModelo(null);
        }
        if (!isset($nf['tipo']) || is_null($nf['tipo'])) {
            $this->setTipo(self::TIPO_SAIDA);
        } else {
            $this->setTipo($nf['tipo']);
        }
        if (!isset($nf['destino']) || is_null($nf['destino'])) {
            $this->setDestino(self::DESTINO_INTERNA);
        } else {
            $this->setDestino($nf['destino']);
        }
        if (!isset($nf['natureza']) || is_null($nf['natureza'])) {
            $this->setNatureza('VENDA PARA CONSUMIDOR FINAL');
        } else {
            $this->setNatureza($nf['natureza']);
        }
        if (isset($nf['codigo'])) {
            $this->setCodigo($nf['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (!isset($nf['indicador']) || is_null($nf['indicador'])) {
            $this->setIndicador(self::INDICADOR_AVISTA);
        } else {
            $this->setIndicador($nf['indicador']);
        }
        if (isset($nf['data_emissao'])) {
            $this->setDataEmissao($nf['data_emissao']);
        } else {
            $this->setDataEmissao(null);
        }
        if (isset($nf['serie'])) {
            $this->setSerie($nf['serie']);
        } else {
            $this->setSerie(null);
        }
        if (!isset($nf['formato']) || is_null($nf['formato'])) {
            $this->setFormato(self::FORMATO_NENHUMA);
        } else {
            $this->setFormato($nf['formato']);
        }
        if (!isset($nf['emissao']) || is_null($nf['emissao'])) {
            $this->setEmissao(self::EMISSAO_NORMAL);
        } else {
            $this->setEmissao($nf['emissao']);
        }
        if (isset($nf['digito_verificador'])) {
            $this->setDigitoVerificador($nf['digito_verificador']);
        } else {
            $this->setDigitoVerificador(null);
        }
        if (!isset($nf['ambiente']) || is_null($nf['ambiente'])) {
            $this->setAmbiente(self::AMBIENTE_HOMOLOGACAO);
        } else {
            $this->setAmbiente($nf['ambiente']);
        }
        if (!isset($nf['finalidade']) || is_null($nf['finalidade'])) {
            $this->setFinalidade(self::FINALIDADE_NORMAL);
        } else {
            $this->setFinalidade($nf['finalidade']);
        }
        if (!isset($nf['consumidor_final']) || is_null($nf['consumidor_final'])) {
            $this->setConsumidorFinal('Y');
        } else {
            $this->setConsumidorFinal($nf['consumidor_final']);
        }
        if (isset($nf['presenca'])) {
            $this->setPresenca($nf['presenca']);
        } else {
            $this->setPresenca(null);
        }
        if (isset($nf['protocolo'])) {
            $this->setProtocolo($nf['protocolo']);
        } else {
            $this->setProtocolo(null);
        }
        return $this;
    }

    public function gerarID()
    {
        $estado = $this->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $estado->checkCodigos();
        $id = sprintf(
            '%02d%02d%02d%s%02d%03d%09d%01d%08d',
            $estado->getCodigo(),
            date('y', $this->getDataEmissao()), // Ano 2 dígitos
            date('m', $this->getDataEmissao()), // Mês 2 dígitos
            $this->getEmitente()->getCNPJ(),
            $this->getModelo(true),
            $this->getSerie(),
            $this->getNumero(),
            $this->getEmissao(true),
            $this->getCodigo()
        );
        return $id.Util::getDAC($id, 11);
    }

    protected function getTotais()
    {
        $total = array();
        $_produtos = $this->getProdutos();
        foreach ($_produtos as $_produto) {
            $imposto_info = $_produto->getImpostoInfo();
            $total['produtos'] += $_produto->getPreco();
            $total['descontos'] += $_produto->getDesconto();
            $total['frete'] += $_produto->getFrete();
            $total['seguro'] += $_produto->getSeguro();
            $total['outros'] += $_produto->getDespesas();
            $total['nota'] += $_produto->getContabilizado();
            $total['tributos'] += $imposto_info['total'];
            $_impostos = $_produto->getImpostos();
            foreach ($_impostos as $_imposto) {
                switch ($_imposto->getGrupo()) {
                    case Imposto::GRUPO_ICMS:
                        if (($_imposto instanceof \Imposto\ICMS\Cobranca) ||
                                ($_imposto instanceof \Imposto\ICMS\Simples\Cobranca)) {
                            $total[$_imposto->getGrupo()] += round($_imposto->getNormal()->getValor(), 2);
                            $total['base'] += $_imposto->getNormal()->getBase();
                        }
                        if (($_imposto instanceof \Imposto\ICMS\Parcial) ||
                                ($_imposto instanceof \Imposto\ICMS\Simples\Parcial)) {
                            $total['icms.st'] += $_imposto->getValor();
                            $total['base.st'] += $_imposto->getBase();
                        } else {
                            $total[$_imposto->getGrupo()] += round($_imposto->getValor(), 2);
                            $total['base'] += $_imposto->getBase();
                        }
                        break;
                    default:
                        $total[$_imposto->getGrupo()] += round($_imposto->getValor(), 2);
                }
            }
        }
        return $total;
    }

    private function getNodeTotal($name = null)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'total':$name);

        // Totais referentes ao ICMS
        $total = $this->getTotais();
        $icms = $dom->createElement('ICMSTot');
        $icms->appendChild($dom->createElement('vBC', Util::toCurrency($total['base'])));
        $icms->appendChild($dom->createElement('vICMS', Util::toCurrency($total['icms'])));
        $icms->appendChild($dom->createElement('vICMSDeson', Util::toCurrency($total['desoneracao'])));
        $icms->appendChild($dom->createElement('vBCST', Util::toCurrency($total['base.st'])));
        $icms->appendChild($dom->createElement('vST', Util::toCurrency($total['icms.st'])));
        $icms->appendChild($dom->createElement('vProd', Util::toCurrency($total['produtos'])));
        $icms->appendChild($dom->createElement('vFrete', Util::toCurrency($total['frete'])));
        $icms->appendChild($dom->createElement('vSeg', Util::toCurrency($total['seguro'])));
        $icms->appendChild($dom->createElement('vDesc', Util::toCurrency($total['descontos'])));
        $icms->appendChild($dom->createElement('vII', Util::toCurrency($total['ii'])));
        $icms->appendChild($dom->createElement('vIPI', Util::toCurrency($total['ipi'])));
        $icms->appendChild($dom->createElement('vPIS', Util::toCurrency($total['pis'])));
        $icms->appendChild($dom->createElement('vCOFINS', Util::toCurrency($total['cofins'])));
        $icms->appendChild($dom->createElement('vOutro', Util::toCurrency($total['outros'])));
        $icms->appendChild($dom->createElement('vNF', Util::toCurrency($total['nota'])));
        $icms->appendChild($dom->createElement('vTotTrib', Util::toCurrency($total['tributos'])));
        $element->appendChild($icms);

        // TODO: Totais referentes ao ISSQN

        // TODO: Retenção de Tributos Federais
        return $element;
    }

    public function getNode($name = null)
    {
        $this->getEmitente()->getEndereco()->checkCodigos();
        $this->setID($this->gerarID());
        $this->setDigitoVerificador(substr($this->getID(), -1, 1));

        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'NFe':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::PORTAL);

        $info = $dom->createElement('infNFe');
        $id = $dom->createAttribute('Id');
        $id->value = $this->getID(true);
        $info->appendChild($id);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $info->appendChild($versao);

        $municipio = $this->getEmitente()->getEndereco()->getMunicipio();
        $estado = $municipio->getEstado();
        $ident = $dom->createElement('ide');
        $ident->appendChild($dom->createElement('cUF', $estado->getCodigo(true)));
        $ident->appendChild($dom->createElement('cNF', $this->getCodigo(true)));
        $ident->appendChild($dom->createElement('natOp', $this->getNatureza(true)));
        $ident->appendChild($dom->createElement('indPag', $this->getIndicador(true)));
        $ident->appendChild($dom->createElement('mod', $this->getModelo(true)));
        $ident->appendChild($dom->createElement('serie', $this->getSerie(true)));
        $ident->appendChild($dom->createElement('nNF', $this->getNumero(true)));
        $ident->appendChild($dom->createElement('dhEmi', $this->getDataEmissao(true)));
        $ident->appendChild($dom->createElement('tpNF', $this->getTipo(true)));
        $ident->appendChild($dom->createElement('idDest', $this->getDestino(true)));
        $ident->appendChild($dom->createElement('cMunFG', $municipio->getCodigo(true)));
        $ident->appendChild($dom->createElement('tpImp', $this->getFormato(true)));
        $ident->appendChild($dom->createElement('tpEmis', $this->getEmissao(true)));
        $ident->appendChild($dom->createElement('cDV', $this->getDigitoVerificador(true)));
        $ident->appendChild($dom->createElement('tpAmb', $this->getAmbiente(true)));
        $ident->appendChild($dom->createElement('finNFe', $this->getFinalidade(true)));
        $ident->appendChild($dom->createElement('indFinal', $this->getConsumidorFinal(true)));
        $ident->appendChild($dom->createElement('indPres', $this->getPresenca(true)));
        $ident->appendChild($dom->createElement('procEmi', 0)); // emissão de NF-e com aplicativo do contribuinte
        $ident->appendChild($dom->createElement('verProc', self::APP_VERSAO));
        if (!is_null($this->getDataMovimentacao())) {
            $ident->appendChild($dom->createElement('dhSaiEnt', $this->getDataMovimentacao(true)));
        }
        if ($this->getEmissao() != self::EMISSAO_NORMAL) {
            $ident->appendChild($dom->createElement('dhCont', $this->getDataContingencia(true)));
            $ident->appendChild($dom->createElement('xJust', $this->getJustificativa(true)));
        }
        $info->appendChild($ident);

        $emitente = $this->getEmitente()->getNode();
        $emitente = $dom->importNode($emitente, true);
        $info->appendChild($emitente);
        if ($this->getAmbiente() == self::AMBIENTE_HOMOLOGACAO && !is_null($this->getCliente())) {
            $this->getCliente()->setNome('NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL');
        }
        if (!is_null($this->getCliente())) {
            $cliente = $this->getCliente()->getNode();
            $cliente = $dom->importNode($cliente, true);
            $info->appendChild($cliente);
        }
        $item = 0;
        $tributos = array();
        $_produtos = $this->getProdutos();
        foreach ($_produtos as $_produto) {
            if (is_null($_produto->getItem())) {
                $item += 1;
                $_produto->setItem($item);
            } else {
                $item = $_produto->getItem();
            }
            if ($this->getAmbiente() == self::AMBIENTE_HOMOLOGACAO) {
                $_produto->setDescricao('NOTA FISCAL EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL');
            }
            $produto = $_produto->getNode();
            $produto = $dom->importNode($produto, true);
            $info->appendChild($produto);
            // Soma os tributos aproximados dos produtos
            $imposto_info = $_produto->getImpostoInfo();
            $tributos['info'] = $imposto_info['info'];
            foreach ($imposto_info as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                $tributos[$key] += $value;
            }
        }
        $total = $this->getNodeTotal();
        $total = $dom->importNode($total, true);
        $info->appendChild($total);
        $transporte = $this->getTransporte()->getNode();
        $transporte = $dom->importNode($transporte, true);
        $info->appendChild($transporte);
        // TODO: adicionar cobrança
        $_pagamentos = $this->getPagamentos();
        foreach ($_pagamentos as $_pagamento) {
            $pagamento = $_pagamento->getNode();
            $pagamento = $dom->importNode($pagamento, true);
            $info->appendChild($pagamento);
        }
        // TODO: adicionar informações adicionais somente na NFC-e?
        $adicional = $dom->createElement('infAdic');
        Produto::addNodeInformacoes($tributos, $adicional, 'infCpl');
        $info->appendChild($adicional);
        // TODO: adicionar exportação
        // TODO: adicionar compra
        // TODO: adicionar cana
        $element->appendChild($info);
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $root = $element;
        $name = is_null($name)?'NFe':$name;
        $nodes = $element->getElementsByTagName($name);
        if ($nodes->length == 0) {
            throw new Exception('Tag "'.$name.'" não encontrada', 404);
        }
        $element = $nodes->item(0);
        $_fields = $element->getElementsByTagName('infNFe');
        if ($_fields->length > 0) {
            $info = $_fields->item(0);
        } else {
            throw new Exception('Tag "infNFe" não encontrada', 404);
        }

        $id = $info->getAttribute('Id');
        if (strlen($id) != 47) {
            throw new Exception('Atributo "Id" inválido, encontrado: "'.$id.'"', 500);
        }
        $this->setID(substr($id, 3));
        $_fields = $info->getElementsByTagName('ide');
        if ($_fields->length > 0) {
            $ident = $_fields->item(0);
        } else {
            throw new Exception('Tag "ide" não encontrada', 404);
        }
        $emitente = new Emitente();
        $_fields = $ident->getElementsByTagName('cUF');
        if ($_fields->length > 0) {
            $codigo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "cUF" do campo "Codigo IBGE da UF" não encontrada', 404);
        }
        $emitente->getEndereco()->getMunicipio()->getEstado()->setCodigo($codigo);
        $_fields = $ident->getElementsByTagName('cNF');
        if ($_fields->length > 0) {
            $codigo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "cNF" do campo "Codigo" não encontrada', 404);
        }
        $this->setCodigo($codigo);
        $_fields = $ident->getElementsByTagName('natOp');
        if ($_fields->length > 0) {
            $natureza = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "natOp" do campo "Natureza" não encontrada', 404);
        }
        $this->setNatureza($natureza);
        $_fields = $ident->getElementsByTagName('indPag');
        if ($_fields->length > 0) {
            $indicador = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "indPag" do campo "Indicador" não encontrada', 404);
        }
        $this->setIndicador($indicador);
        $_fields = $ident->getElementsByTagName('mod');
        if ($_fields->length > 0) {
            $modelo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "mod" do campo "Modelo" não encontrada', 404);
        }
        $this->setModelo($modelo);
        $_fields = $ident->getElementsByTagName('serie');
        if ($_fields->length > 0) {
            $serie = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "serie" do campo "Serie" não encontrada', 404);
        }
        $this->setSerie($serie);
        $_fields = $ident->getElementsByTagName('nNF');
        if ($_fields->length > 0) {
            $numero = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "nNF" do campo "Numero" não encontrada', 404);
        }
        $this->setNumero($numero);
        $_fields = $ident->getElementsByTagName('dhEmi');
        if ($_fields->length > 0) {
            $data_emissao = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "dhEmi" do campo "DataEmissao" não encontrada', 404);
        }
        $this->setDataEmissao($data_emissao);
        $_fields = $ident->getElementsByTagName('tpNF');
        if ($_fields->length > 0) {
            $tipo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "tpNF" do campo "Tipo" não encontrada', 404);
        }
        $this->setTipo($tipo);
        $_fields = $ident->getElementsByTagName('idDest');
        if ($_fields->length > 0) {
            $destino = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "idDest" do campo "Destino" não encontrada', 404);
        }
        $this->setDestino($destino);
        $_fields = $ident->getElementsByTagName('cMunFG');
        if ($_fields->length > 0) {
            $codigo = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "cMunFG" do campo "Codigo IBGE do município" não encontrada', 404);
        }
        $emitente->getEndereco()->getMunicipio()->setCodigo($codigo);
        $_fields = $ident->getElementsByTagName('dhSaiEnt');
        $data_movimentacao = null;
        if ($_fields->length > 0) {
            $data_movimentacao = $_fields->item(0)->nodeValue;
        }
        $this->setDataMovimentacao($data_movimentacao);
        $_fields = $ident->getElementsByTagName('tpImp');
        if ($_fields->length > 0) {
            $formato = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "tpImp" do campo "Formato" não encontrada', 404);
        }
        $this->setFormato($formato);
        $_fields = $ident->getElementsByTagName('tpEmis');
        if ($_fields->length > 0) {
            $emissao = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "tpEmis" do campo "Emissao" não encontrada', 404);
        }
        $this->setEmissao($emissao);
        $_fields = $ident->getElementsByTagName('cDV');
        if ($_fields->length > 0) {
            $digito_verificador = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "cDV" do campo "DigitoVerificador" não encontrada', 404);
        }
        $this->setDigitoVerificador($digito_verificador);
        $_fields = $ident->getElementsByTagName('tpAmb');
        if ($_fields->length > 0) {
            $ambiente = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "tpAmb" do campo "Ambiente" não encontrada', 404);
        }
        $this->setAmbiente($ambiente);
        $_fields = $ident->getElementsByTagName('finNFe');
        if ($_fields->length > 0) {
            $finalidade = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "finNFe" do campo "Finalidade" não encontrada', 404);
        }
        $this->setFinalidade($finalidade);
        $_fields = $ident->getElementsByTagName('indFinal');
        if ($_fields->length > 0) {
            $consumidor_final = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "indFinal" do campo "ConsumidorFinal" não encontrada', 404);
        }
        $this->setConsumidorFinal($consumidor_final);
        $_fields = $ident->getElementsByTagName('indPres');
        if ($_fields->length > 0) {
            $presenca = $_fields->item(0)->nodeValue;
        } else {
            throw new Exception('Tag "indPres" do campo "Presenca" não encontrada', 404);
        }
        $this->setPresenca($presenca);
        $_fields = $ident->getElementsByTagName('dhCont');
        $data_contingencia = null;
        if ($_fields->length > 0) {
            $data_contingencia = $_fields->item(0)->nodeValue;
        }
        $this->setDataContingencia($data_contingencia);
        $_fields = $ident->getElementsByTagName('xJust');
        $justificativa = null;
        if ($_fields->length > 0) {
            $justificativa = $_fields->item(0)->nodeValue;
        }
        $this->setJustificativa($justificativa);

        $_fields = $info->getElementsByTagName('emit');
        if ($_fields->length > 0) {
            $emitente->loadNode($_fields->item(0), 'emit');
        } else {
            throw new Exception('Tag "emit" do objeto "Emitente" não encontrada', 404);
        }
        $this->setEmitente($emitente);
        $_fields = $info->getElementsByTagName('dest');
        $cliente = null;
        if ($_fields->length > 0) {
            $cliente = new Cliente();
            $cliente->loadNode($_fields->item(0), 'dest');
        }
        $this->setCliente($cliente);
        $produtos = array();
        $_items = $info->getElementsByTagName('det');
        foreach ($_items as $_item) {
            $produto = new Produto();
            $produto->loadNode($_item, 'det');
            $produtos[] = $produto;
        }
        $this->setProdutos($produtos);
        $_fields = $info->getElementsByTagName('transp');
        $transporte = null;
        if ($_fields->length > 0) {
            $transporte = new Transporte();
            $transporte->loadNode($_fields->item(0), 'transp');
        }
        $this->setTransporte($transporte);
        $pagamentos = array();
        $_items = $info->getElementsByTagName('pag');
        foreach ($_items as $_item) {
            $pagamento = new Pagamento();
            $pagamento->loadNode($_item, 'pag');
            $pagamentos[] = $pagamento;
        }
        $this->setPagamentos($pagamentos);

        $_fields = $root->getElementsByTagName('protNFe');
        $protocolo = null;
        if ($_fields->length > 0) {
            $protocolo = new NF\Protocolo();
            $protocolo->loadNode($_fields->item(0), 'infProt');
        }
        $this->setProtocolo($protocolo);
        return $element;
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
        $adapter->sign($dom, 'infNFe');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(dirname(__FILE__)) . '/schema';
        if (is_null($this->getProtocolo())) {
            $xsd_file = $xsd_path . '/nfe_v3.10.xsd';
        } else {
            $xsd_file = $xsd_path . '/procNFe_v3.10.xsd';
        }
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

    /**
     * Adiciona o protocolo no XML da nota
     */
    public function addProtocolo($dom)
    {
        if (is_null($this->getProtocolo())) {
            throw new Exception('O protocolo não foi informado na nota "'.$this->getID().'"', 404);
        }
        $nfe = $dom->getElementsByTagName('NFe')->item(0);
        // Corrige xmlns:default
        $nfe_xml = $dom->saveXML($nfe);

        $element = $dom->createElement('nfeProc');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);
        $dom->removeChild($nfe);
        // Corrige xmlns:default
        $nfe = $dom->createElement('NFe', 0);

        $element->appendChild($nfe);
        $info = $this->getProtocolo()->getNode();
        $info = $dom->importNode($info, true);
        $element->appendChild($info);
        $dom->appendChild($element);
        // Corrige xmlns:default
        $xml = $dom->saveXML();
        $xml = str_replace('<NFe>0</NFe>', $nfe_xml, $xml);
        $dom->loadXML($xml);

        return $dom;
    }
}
