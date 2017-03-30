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
use NFe\Common\Node;
use NFe\Task\Protocolo;
use NFe\Entity\Total;
use NFe\Entity\Imposto;
use NFe\Entity\Produto;
use NFe\Entity\Emitente;
use NFe\Entity\Pagamento;
use NFe\Entity\Transporte;
use NFe\Entity\Destinatario;
use NFe\Exception\ValidationException;
use FR3D\XmlDSig\Adapter\AdapterInterface;
use FR3D\XmlDSig\Adapter\XmlseclibsAdapter;

/**
 * Classe base para a formação da nota fiscal
 */
abstract class Nota implements Node
{

    /**
     * Versão da nota fiscal
     */
    const VERSAO = '3.10';

    /**
     * Versão do aplicativo gerador da nota
     */
    const APP_VERSAO = '1.0';

    /**
     * Portal da nota fiscal
     */
    const PORTAL = 'http://www.portalfiscal.inf.br/nfe';

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     */
    const MODELO_NFE = 'nfe';
    const MODELO_NFCE = 'nfce';

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

    /**
     * Chave da nota fiscal
     */
    private $id;
    /**
     * Número do Documento Fiscal
     */
    private $numero;
    /**
     * Emitente da nota fiscal
     */
    private $emitente;
    /**
     * Destinatário que receberá os produtos
     */
    private $destinatario;
    /**
     * Produtos adicionados na nota
     */
    private $produtos;
    /**
     * Informações de trasnporte da mercadoria
     */
    private $transporte;
    /**
     * Pagamentos realizados
     */
    private $pagamentos;
    /**
     * Data e Hora da saída ou de entrada da mercadoria / produto
     */
    private $data_movimentacao;
    /**
     * Informar a data e hora de entrada em contingência
     */
    private $data_contingencia;
    /**
     * Informar a Justificativa da entrada em contingência
     */
    private $justificativa;
    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     */
    private $modelo;
    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     */
    private $tipo;
    /**
     * Identificador de Local de destino da operação
     * (1-Interna;2-Interestadual;3-Exterior)
     */
    private $destino;
    /**
     * Descrição da Natureza da Operação
     */
    private $natureza;
    /**
     * Código numérico que compõe a Chave de Acesso. Número aleatório gerado
     * pelo emitente para cada NF-e.
     */
    private $codigo;
    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo; 2 – outros.
     */
    private $indicador;
    /**
     * Data e Hora de emissão do Documento Fiscal
     */
    private $data_emissao;
    /**
     * Série do Documento Fiscal: série normal 0-889, Avulsa Fisco 890-899,
     * SCAN 900-999
     */
    private $serie;
    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     */
    private $formato;
    /**
     * Forma de emissão da NF-e
     */
    private $emissao;
    /**
     * Digito Verificador da Chave de Acesso da NF-e
     */
    private $digito_verificador;
    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     */
    private $ambiente;
    /**
     * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
     * NFe de ajuste, 4 - Devolução/Retorno
     */
    private $finalidade;
    /**
     * Indica operação com consumidor final (0-Não;1-Consumidor Final)
     */
    private $consumidor_final;
    /**
     * Indicador de presença do comprador no estabelecimento comercial no
     * momento da oepração (0-Não se aplica, ex.: Nota Fiscal complementar ou
     * de ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
     * presencial, teleatendimento;4-NFC-e entrega em domicílio;9-Não
     * presencial, outros)
     */
    private $presenca;
    /**
     * Dados dos totais da NF-e
     */
    private $total;
    /**
     * Informações adicionais de interesse do Fisco
     */
    private $adicionais;
    /**
     * Campo de uso livre do contribuinte informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     */
    private $observacoes;
    /**
     * Campo de uso exclusivo do Fisco informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     */
    private $informacoes;
    /**
     * Protocolo de autorização da nota, informado apenas quando a nota for
     * enviada e autorizada
     */
    private $protocolo;

    /**
     * Constroi uma instância de Nota vazia
     * @param  array $nota Array contendo dados da Nota
     */
    public function __construct($nota = array())
    {
        $this->fromArray($nota);
    }

    /**
     * Chave da nota fiscal
     * @param boolean $normalize informa se o id deve estar no formato do XML
     * @return mixed id da Nota
     */
    public function getID($normalize = false)
    {
        if (!$normalize) {
            return $this->id;
        }
        return 'NFe'.$this->id;
    }

    /**
     * Altera o valor do ID para o informado no parâmetro
     * @param mixed $id novo valor para ID
     * @return Nota A própria instância da classe
     */
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Número do Documento Fiscal
     * @param boolean $normalize informa se o numero deve estar no formato do XML
     * @return mixed numero da Nota
     */
    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    /**
     * Altera o valor do Numero para o informado no parâmetro
     * @param mixed $numero novo valor para Numero
     * @return Nota A própria instância da classe
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Emitente da nota fiscal
     * @return mixed emitente da Nota
     */
    public function getEmitente()
    {
        return $this->emitente;
    }

    /**
     * Altera o valor do Emitente para o informado no parâmetro
     * @param mixed $emitente novo valor para Emitente
     * @return Nota A própria instância da classe
     */
    public function setEmitente($emitente)
    {
        $this->emitente = $emitente;
        return $this;
    }

    /**
     * Destinatário que receberá os produtos
     * @return mixed destinatario da Nota
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }

    /**
     * Altera o valor do Destinatario para o informado no parâmetro
     * @param mixed $destinatario novo valor para Destinatario
     * @return Nota A própria instância da classe
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;
        return $this;
    }

    /**
     * Produtos adicionados na nota
     * @return mixed produtos da Nota
     */
    public function getProdutos()
    {
        return $this->produtos;
    }

    /**
     * Altera o valor do Produtos para o informado no parâmetro
     * @param mixed $produtos novo valor para Produtos
     * @return Nota A própria instância da classe
     */
    public function setProdutos($produtos)
    {
        $this->produtos = $produtos;
        return $this;
    }

    /**
     * Adiciona um(a) Produto para a lista de produto
     * @param Produto $produto Instância do Produto que será adicionada
     * @return Nota A própria instância da classe
     */
    public function addProduto($produto)
    {
        $this->produtos[] = $produto;
        return $this;
    }

    /**
     * Informações de trasnporte da mercadoria
     * @return mixed transporte da Nota
     */
    public function getTransporte()
    {
        return $this->transporte;
    }

    /**
     * Altera o valor da Transporte para o informado no parâmetro
     * @param mixed $transporte novo valor para Transporte
     * @return Nota A própria instância da classe
     */
    public function setTransporte($transporte)
    {
        $this->transporte = $transporte;
        return $this;
    }

    /**
     * Pagamentos realizados
     * @return mixed pagamentos da Nota
     */
    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    /**
     * Altera o valor do Pagamentos para o informado no parâmetro
     * @param mixed $pagamentos novo valor para Pagamentos
     * @return Nota A própria instância da classe
     */
    public function setPagamentos($pagamentos)
    {
        $this->pagamentos = $pagamentos;
        return $this;
    }

    /**
     * Adiciona um(a) Pagamento para a lista de pagamento
     * @param Pagamento $pagamento Instância do Pagamento que será adicionada
     * @return Nota A própria instância da classe
     */
    public function addPagamento($pagamento)
    {
        $this->pagamentos[] = $pagamento;
        return $this;
    }

    /**
     * Data e Hora da saída ou de entrada da mercadoria / produto
     * @param boolean $normalize informa se a data_movimentacao deve estar no formato do XML
     * @return mixed data_movimentacao da Nota
     */
    public function getDataMovimentacao($normalize = false)
    {
        if (!$normalize || is_null($this->data_movimentacao)) {
            return $this->data_movimentacao;
        }
        return Util::toDateTime($this->data_movimentacao);
    }

    /**
     * Altera o valor da DataMovimentacao para o informado no parâmetro
     * @param mixed $data_movimentacao novo valor para DataMovimentacao
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se a data_contingencia deve estar no formato do XML
     * @return mixed data_contingencia da Nota
     */
    public function getDataContingencia($normalize = false)
    {
        if (!$normalize || is_null($this->data_contingencia)) {
            return $this->data_contingencia;
        }
        return Util::toDateTime($this->data_contingencia);
    }

    /**
     * Altera o valor da DataContingencia para o informado no parâmetro
     * @param mixed $data_contingencia novo valor para DataContingencia
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se a justificativa deve estar no formato do XML
     * @return mixed justificativa da Nota
     */
    public function getJustificativa($normalize = false)
    {
        if (!$normalize) {
            return $this->justificativa;
        }
        return $this->justificativa;
    }

    /**
     * Altera o valor da Justificativa para o informado no parâmetro
     * @param mixed $justificativa novo valor para Justificativa
     * @return Nota A própria instância da classe
     */
    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
        return $this;
    }

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     * @param boolean $normalize informa se o modelo deve estar no formato do XML
     * @return mixed modelo da Nota
     */
    public function getModelo($normalize = false)
    {
        if (!$normalize) {
            return $this->modelo;
        }
        switch ($this->modelo) {
            case self::MODELO_NFE:
                return '55';
            case self::MODELO_NFCE:
                return '65';
        }
        return $this->modelo;
    }

    /**
     * Altera o valor do Modelo para o informado no parâmetro
     * @param mixed $modelo novo valor para Modelo
     * @return Nota A própria instância da classe
     */
    public function setModelo($modelo)
    {
        switch ($modelo) {
            case '55':
                $modelo = self::MODELO_NFE;
                break;
            case '65':
                $modelo = self::MODELO_NFCE;
                break;
        }
        $this->modelo = $modelo;
        return $this;
    }

    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     * @param boolean $normalize informa se o tipo deve estar no formato do XML
     * @return mixed tipo da Nota
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

    /**
     * Altera o valor do Tipo para o informado no parâmetro
     * @param mixed $tipo novo valor para Tipo
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se o destino deve estar no formato do XML
     * @return mixed destino da Nota
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

    /**
     * Altera o valor do Destino para o informado no parâmetro
     * @param mixed $destino novo valor para Destino
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se a natureza deve estar no formato do XML
     * @return mixed natureza da Nota
     */
    public function getNatureza($normalize = false)
    {
        if (!$normalize) {
            return $this->natureza;
        }
        return $this->natureza;
    }

    /**
     * Altera o valor da Natureza para o informado no parâmetro
     * @param mixed $natureza novo valor para Natureza
     * @return Nota A própria instância da classe
     */
    public function setNatureza($natureza)
    {
        $this->natureza = $natureza;
        return $this;
    }

    /**
     * Código numérico que compõe a Chave de Acesso. Número aleatório gerado
     * pelo emitente para cada NF-e.
     * @param boolean $normalize informa se o codigo deve estar no formato do XML
     * @return mixed codigo da Nota
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return Util::padDigit($this->codigo % 100000000, 8);
    }

    /**
     * Altera o valor do Codigo para o informado no parâmetro
     * @param mixed $codigo novo valor para Codigo
     * @return Nota A própria instância da classe
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo; 2 – outros.
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
            case self::INDICADOR_OUTROS:
                return '2';
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
            case '2':
                $indicador = self::INDICADOR_OUTROS;
                break;
        }
        $this->indicador = $indicador;
        return $this;
    }

    /**
     * Data e Hora de emissão do Documento Fiscal
     * @param boolean $normalize informa se o data_emissao deve estar no formato do XML
     * @return mixed data_emissao da Nota
     */
    public function getDataEmissao($normalize = false)
    {
        if (!$normalize) {
            return $this->data_emissao;
        }
        return Util::toDateTime($this->data_emissao);
    }

    /**
     * Altera o valor do DataEmissao para o informado no parâmetro
     * @param mixed $data_emissao novo valor para DataEmissao
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se o serie deve estar no formato do XML
     * @return mixed serie da Nota
     */
    public function getSerie($normalize = false)
    {
        if (!$normalize) {
            return $this->serie;
        }
        return $this->serie;
    }

    /**
     * Altera o valor do Serie para o informado no parâmetro
     * @param mixed $serie novo valor para Serie
     * @return Nota A própria instância da classe
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     * @param boolean $normalize informa se o formato deve estar no formato do XML
     * @return mixed formato da Nota
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

    /**
     * Altera o valor do Formato para o informado no parâmetro
     * @param mixed $formato novo valor para Formato
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se o emissao deve estar no formato do XML
     * @return mixed emissao da Nota
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

    /**
     * Altera o valor do Emissao para o informado no parâmetro
     * @param mixed $emissao novo valor para Emissao
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se o digito_verificador deve estar no formato do XML
     * @return mixed digito_verificador da Nota
     */
    public function getDigitoVerificador($normalize = false)
    {
        if (!$normalize) {
            return $this->digito_verificador;
        }
        return $this->digito_verificador;
    }

    /**
     * Altera o valor do DigitoVerificador para o informado no parâmetro
     * @param mixed $digito_verificador novo valor para DigitoVerificador
     * @return Nota A própria instância da classe
     */
    public function setDigitoVerificador($digito_verificador)
    {
        $this->digito_verificador = $digito_verificador;
        return $this;
    }

    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     * @param boolean $normalize informa se o ambiente deve estar no formato do XML
     * @return mixed ambiente da Nota
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

    /**
     * Altera o valor do Ambiente para o informado no parâmetro
     * @param mixed $ambiente novo valor para Ambiente
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se a finalidade deve estar no formato do XML
     * @return mixed finalidade da Nota
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

    /**
     * Altera o valor da Finalidade para o informado no parâmetro
     * @param mixed $finalidade novo valor para Finalidade
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se o consumidor_final deve estar no formato do XML
     * @return mixed consumidor_final da Nota
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
     * @return boolean informa se o ConsumidorFinal está habilitado
     */
    public function isConsumidorFinal()
    {
        return $this->consumidor_final == 'Y';
    }

    /**
     * Altera o valor do ConsumidorFinal para o informado no parâmetro
     * @param mixed $consumidor_final novo valor para ConsumidorFinal
     * @return Nota A própria instância da classe
     */
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
     * @param boolean $normalize informa se a presenca deve estar no formato do XML
     * @return mixed presenca da Nota
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

    /**
     * Altera o valor da Presenca para o informado no parâmetro
     * @param mixed $presenca novo valor para Presenca
     * @return Nota A própria instância da classe
     */
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

    /**
     * Dados dos totais da NF-e
     * @return mixed total da Nota
     */
    public function getTotal()
    {
        return $this->total;
    }
    
    /**
     * Altera o valor do Total para o informado no parâmetro
     * @param mixed $total novo valor para Total
     * @return Nota A própria instância da classe
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Informações adicionais de interesse do Fisco
     * @param boolean $normalize informa se a adicionais deve estar no formato do XML
     * @return mixed adicionais da Nota
     */
    public function getAdicionais($normalize = false)
    {
        if (!$normalize) {
            return $this->adicionais;
        }
        return $this->adicionais;
    }
    
    /**
     * Altera o valor da Adicionais para o informado no parâmetro
     * @param mixed $adicionais novo valor para Adicionais
     * @return Nota A própria instância da classe
     */
    public function setAdicionais($adicionais)
    {
        $this->adicionais = $adicionais;
        return $this;
    }

    /**
     * Campo de uso livre do contribuinte informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     * @return mixed observacoes da Nota
     */
    public function getObservacoes()
    {
        return $this->observacoes;
    }
    
    /**
     * Altera o valor da Observacoes para o informado no parâmetro
     * @param mixed $observacoes novo valor para Observacoes
     * @return Nota A própria instância da classe
     */
    public function setObservacoes($observacoes)
    {
        $this->observacoes = $observacoes;
        return $this;
    }

    /**
     * Adiciona um(a) Observacao para a lista de observacao
     * @param Observacao $observacao Instância da Observacao que será adicionada
     * @return Nota A própria instância da classe
     */
    public function addObservacao($campo, $observacao)
    {
        $this->observacoes[] = array('campo' => $campo, 'valor' => $observacao);
        return $this;
    }

    /**
     * Campo de uso exclusivo do Fisco informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     * @return mixed informacoes da Nota
     */
    public function getInformacoes()
    {
        return $this->informacoes;
    }
    
    /**
     * Altera o valor da Informacoes para o informado no parâmetro
     * @param mixed $informacoes novo valor para Informacoes
     * @return Nota A própria instância da classe
     */
    public function setInformacoes($informacoes)
    {
        $this->informacoes = $informacoes;
        return $this;
    }

    /**
     * Adiciona um(a) Informacao para a lista de informacao
     * @param Informacao $informacao Instância da Informacao que será adicionada
     * @return Nota A própria instância da classe
     */
    public function addInformacao($campo, $informacao)
    {
        $this->informacoes[] = array('campo' => $campo, 'valor' => $informacao);
        return $this;
    }

    /**
     * Protocolo de autorização da nota, informado apenas quando a nota for
     * enviada e autorizada
     */
    public function getProtocolo()
    {
        return $this->protocolo;
    }

    public function setProtocolo($protocolo)
    {
        $this->protocolo = $protocolo;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $nota = array();
        $nota['id'] = $this->getID();
        $nota['numero'] = $this->getNumero();
        if (!is_null($this->getEmitente()) && $recursive) {
            $nota['emitente'] = $this->getEmitente()->toArray($recursive);
        } else {
            $nota['emitente'] = $this->getEmitente();
        }
        if (!is_null($this->getDestinatario()) && $recursive) {
            $nota['destinatario'] = $this->getDestinatario()->toArray($recursive);
        } else {
            $nota['destinatario'] = $this->getDestinatario();
        }
        if ($recursive) {
            $produtos = array();
            $_produtos = $this->getProdutos();
            foreach ($_produtos as $_produto) {
                $produtos[] = $_produto->toArray($recursive);
            }
            $nota['produtos'] = $produtos;
        } else {
            $nota['produtos'] = $this->getProdutos();
        }
        if (!is_null($this->getTransporte()) && $recursive) {
            $nota['transporte'] = $this->getTransporte()->toArray($recursive);
        } else {
            $nota['transporte'] = $this->getTransporte();
        }
        if ($recursive) {
            $pagamentos = array();
            $_pagamentos = $this->getPagamentos();
            foreach ($_pagamentos as $_pagamento) {
                $pagamentos[] = $_pagamento->toArray($recursive);
            }
            $nota['pagamentos'] = $pagamentos;
        } else {
            $nota['pagamentos'] = $this->getPagamentos();
        }
        $nota['data_movimentacao'] = $this->getDataMovimentacao($recursive);
        $nota['data_contingencia'] = $this->getDataContingencia($recursive);
        $nota['justificativa'] = $this->getJustificativa();
        $nota['modelo'] = $this->getModelo();
        $nota['tipo'] = $this->getTipo();
        $nota['destino'] = $this->getDestino();
        $nota['natureza'] = $this->getNatureza();
        $nota['codigo'] = $this->getCodigo();
        $nota['indicador'] = $this->getIndicador();
        $nota['data_emissao'] = $this->getDataEmissao($recursive);
        $nota['serie'] = $this->getSerie();
        $nota['formato'] = $this->getFormato();
        $nota['emissao'] = $this->getEmissao();
        $nota['digito_verificador'] = $this->getDigitoVerificador();
        $nota['ambiente'] = $this->getAmbiente();
        $nota['finalidade'] = $this->getFinalidade();
        $nota['consumidor_final'] = $this->getConsumidorFinal();
        $nota['presenca'] = $this->getPresenca();
        if (!is_null($this->getTotal()) && $recursive) {
            $nota['total'] = $this->getTotal()->toArray($recursive);
        } else {
            $nota['total'] = $this->getTotal();
        }
        $nota['adicionais'] = $this->getAdicionais();
        $nota['observacoes'] = $this->getObservacoes();
        $nota['informacoes'] = $this->getInformacoes();
        if (!is_null($this->getProtocolo()) && $recursive) {
            $nota['protocolo'] = $this->getProtocolo()->toArray($recursive);
        } else {
            $nota['protocolo'] = $this->getProtocolo();
        }
        return $nota;
    }

    public function fromArray($nota = array())
    {
        if ($nota instanceof Nota) {
            $nota = $nota->toArray();
        } elseif (!is_array($nota)) {
            return $this;
        }
        if (isset($nota['id'])) {
            $this->setID($nota['id']);
        } else {
            $this->setID(null);
        }
        if (isset($nota['numero'])) {
            $this->setNumero($nota['numero']);
        } else {
            $this->setNumero(null);
        }
        if (!isset($nota['emitente']) || is_null($nota['emitente'])) {
            $this->setEmitente(new Emitente());
        } else {
            $this->setEmitente($nota['emitente']);
        }
        if (!isset($nota['destinatario']) || is_null($nota['destinatario'])) {
            $this->setDestinatario(new Destinatario());
        } else {
            $this->setDestinatario($nota['destinatario']);
        }
        if (!isset($nota['produtos']) || is_null($nota['produtos'])) {
            $this->setProdutos(array());
        } else {
            $this->setProdutos($nota['produtos']);
        }
        if (!isset($nota['transporte']) || is_null($nota['transporte'])) {
            $this->setTransporte(new Transporte());
        } else {
            $this->setTransporte($nota['transporte']);
        }
        if (!isset($nota['pagamentos']) || is_null($nota['pagamentos'])) {
            $this->setPagamentos(array());
        } else {
            $this->setPagamentos($nota['pagamentos']);
        }
        if (isset($nota['data_movimentacao'])) {
            $this->setDataMovimentacao($nota['data_movimentacao']);
        } else {
            $this->setDataMovimentacao(null);
        }
        if (isset($nota['data_contingencia'])) {
            $this->setDataContingencia($nota['data_contingencia']);
        } else {
            $this->setDataContingencia(null);
        }
        if (isset($nota['justificativa'])) {
            $this->setJustificativa($nota['justificativa']);
        } else {
            $this->setJustificativa(null);
        }
        if (isset($nota['modelo'])) {
            $this->setModelo($nota['modelo']);
        } else {
            $this->setModelo(null);
        }
        if (!isset($nota['tipo']) || is_null($nota['tipo'])) {
            $this->setTipo(self::TIPO_SAIDA);
        } else {
            $this->setTipo($nota['tipo']);
        }
        if (!isset($nota['destino']) || is_null($nota['destino'])) {
            $this->setDestino(self::DESTINO_INTERNA);
        } else {
            $this->setDestino($nota['destino']);
        }
        if (!isset($nota['natureza']) || is_null($nota['natureza'])) {
            $this->setNatureza('VENDA PARA CONSUMIDOR FINAL');
        } else {
            $this->setNatureza($nota['natureza']);
        }
        if (isset($nota['codigo'])) {
            $this->setCodigo($nota['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (!isset($nota['indicador']) || is_null($nota['indicador'])) {
            $this->setIndicador(self::INDICADOR_AVISTA);
        } else {
            $this->setIndicador($nota['indicador']);
        }
        if (isset($nota['data_emissao'])) {
            $this->setDataEmissao($nota['data_emissao']);
        } else {
            $this->setDataEmissao(null);
        }
        if (isset($nota['serie'])) {
            $this->setSerie($nota['serie']);
        } else {
            $this->setSerie(null);
        }
        if (!isset($nota['formato']) || is_null($nota['formato'])) {
            $this->setFormato(self::FORMATO_NENHUMA);
        } else {
            $this->setFormato($nota['formato']);
        }
        if (!isset($nota['emissao']) || is_null($nota['emissao'])) {
            $this->setEmissao(self::EMISSAO_NORMAL);
        } else {
            $this->setEmissao($nota['emissao']);
        }
        if (isset($nota['digito_verificador'])) {
            $this->setDigitoVerificador($nota['digito_verificador']);
        } else {
            $this->setDigitoVerificador(null);
        }
        if (!isset($nota['ambiente']) || is_null($nota['ambiente'])) {
            $this->setAmbiente(self::AMBIENTE_HOMOLOGACAO);
        } else {
            $this->setAmbiente($nota['ambiente']);
        }
        if (!isset($nota['finalidade']) || is_null($nota['finalidade'])) {
            $this->setFinalidade(self::FINALIDADE_NORMAL);
        } else {
            $this->setFinalidade($nota['finalidade']);
        }
        if (!isset($nota['consumidor_final']) || is_null($nota['consumidor_final'])) {
            $this->setConsumidorFinal('Y');
        } else {
            $this->setConsumidorFinal($nota['consumidor_final']);
        }
        if (isset($nota['presenca'])) {
            $this->setPresenca($nota['presenca']);
        } else {
            $this->setPresenca(null);
        }
        if (!isset($nota['total'])) {
            $this->setTotal(new Total());
        } else {
            $this->setTotal($nota['total']);
        }
        if (!array_key_exists('adicionais', $nota)) {
            $this->setAdicionais(null);
        } else {
            $this->setAdicionais($nota['adicionais']);
        }
        if (!array_key_exists('observacoes', $nota)) {
            $this->setObservacoes(null);
        } else {
            $this->setObservacoes($nota['observacoes']);
        }
        if (!array_key_exists('informacoes', $nota)) {
            $this->setInformacoes(null);
        } else {
            $this->setInformacoes($nota['informacoes']);
        }
        if (isset($nota['protocolo'])) {
            $this->setProtocolo($nota['protocolo']);
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
        $total['produtos'] = 0.00;
        $total['desconto'] = 0.00;
        $total['frete'] = 0.00;
        $total['seguro'] = 0.00;
        $total['despesas'] = 0.00;
        $total['tributos'] = 0.00;
        $total['icms'] = 0.00;
        $total['icms.st'] = 0.00;
        $total['base'] = 0.00;
        $total['base.st'] = 0.00;
        $total['ii'] = 0.00;
        $total['ipi'] = 0.00;
        $total['pis'] = 0.00;
        $total['cofins'] = 0.00;
        $total['desoneracao'] = 0.00;
        $_produtos = $this->getProdutos();
        foreach ($_produtos as $_produto) {
            if (!$_produto->getMultiplicador()) {
                continue;
            }
            $imposto_info = $_produto->getImpostoInfo();
            $total['produtos'] += round($_produto->getPreco(), 2);
            $total['desconto'] += round($_produto->getDesconto(), 2);
            $total['frete'] += round($_produto->getFrete(), 2);
            $total['seguro'] += round($_produto->getSeguro(), 2);
            $total['despesas'] += round($_produto->getDespesas(), 2);
            $total['tributos'] += round($imposto_info['total'], 2);
            $_impostos = $_produto->getImpostos();
            foreach ($_impostos as $_imposto) {
                switch ($_imposto->getGrupo()) {
                    case Imposto::GRUPO_ICMS:
                        if (($_imposto instanceof \NFe\Entity\Imposto\ICMS\Cobranca) ||
                                ($_imposto instanceof \NFe\Entity\Imposto\ICMS\Simples\Cobranca)) {
                            $total[$_imposto->getGrupo()] += round($_imposto->getNormal()->getValor(), 2);
                            $total['base'] += round($_imposto->getNormal()->getBase(), 2);
                        }
                        if (($_imposto instanceof \NFe\Entity\Imposto\ICMS\Parcial) ||
                                ($_imposto instanceof \NFe\Entity\Imposto\ICMS\Simples\Parcial)) {
                            $total['icms.st'] += round($_imposto->getValor(), 2);
                            $total['base.st'] += round($_imposto->getBase(), 2);
                        } else {
                            $total[$_imposto->getGrupo()] += round($_imposto->getValor(), 2);
                            $total['base'] += round($_imposto->getBase(), 2);
                        }
                        break;
                    default:
                        $total[$_imposto->getGrupo()] += round($_imposto->getValor(), 2);
                }
            }
        }
        $produtos = round($total['produtos'], 2) - round($total['desconto'], 2);
        $servicos = round($total['frete'], 2) + round($total['seguro'], 2) + round($total['despesas'], 2);
        $impostos = round($total['ii'], 2) + round($total['ipi'], 2) + round($total['icms.st'], 2);
        $impostos = $impostos - round($total['desoneracao'], 2);
        $total['nota'] = $produtos + $servicos + $impostos;
        return $total;
    }

    private function getNodeTotal($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'total':$name);

        // Totais referentes ao ICMS
        $total = $this->getTotais();
        $icms = $dom->createElement('ICMSTot');
        Util::appendNode($icms, 'vBC', Util::toCurrency($total['base']));
        Util::appendNode($icms, 'vICMS', Util::toCurrency($total['icms']));
        Util::appendNode($icms, 'vICMSDeson', Util::toCurrency($total['desoneracao']));
        Util::appendNode($icms, 'vBCST', Util::toCurrency($total['base.st']));
        Util::appendNode($icms, 'vST', Util::toCurrency($total['icms.st']));
        Util::appendNode($icms, 'vProd', Util::toCurrency($total['produtos']));
        Util::appendNode($icms, 'vFrete', Util::toCurrency($total['frete']));
        Util::appendNode($icms, 'vSeg', Util::toCurrency($total['seguro']));
        Util::appendNode($icms, 'vDesc', Util::toCurrency($total['desconto']));
        Util::appendNode($icms, 'vII', Util::toCurrency($total['ii']));
        Util::appendNode($icms, 'vIPI', Util::toCurrency($total['ipi']));
        Util::appendNode($icms, 'vPIS', Util::toCurrency($total['pis']));
        Util::appendNode($icms, 'vCOFINS', Util::toCurrency($total['cofins']));
        Util::appendNode($icms, 'vOutro', Util::toCurrency($total['despesas']));
        Util::appendNode($icms, 'vNF', Util::toCurrency($total['nota']));
        Util::appendNode($icms, 'vTotTrib', Util::toCurrency($total['tributos']));
        $element->appendChild($icms);
        $this->setTotal(new Total($total));
        $this->getTotal()->setProdutos($total['produtos']);

        // TODO: Totais referentes ao ISSQN

        // TODO: Retenção de Tributos Federais
        return $element;
    }

    public function getNode($name = null)
    {
        $this->getEmitente()->getEndereco()->checkCodigos();
        $this->setID($this->gerarID());
        $this->setDigitoVerificador(substr($this->getID(), -1, 1));

        $dom = new \DOMDocument('1.0', 'UTF-8');
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
        Util::appendNode($ident, 'cUF', $estado->getCodigo(true));
        Util::appendNode($ident, 'cNF', $this->getCodigo(true));
        Util::appendNode($ident, 'natOp', $this->getNatureza(true));
        Util::appendNode($ident, 'indPag', $this->getIndicador(true));
        Util::appendNode($ident, 'mod', $this->getModelo(true));
        Util::appendNode($ident, 'serie', $this->getSerie(true));
        Util::appendNode($ident, 'nNF', $this->getNumero(true));
        Util::appendNode($ident, 'dhEmi', $this->getDataEmissao(true));
        Util::appendNode($ident, 'tpNF', $this->getTipo(true));
        Util::appendNode($ident, 'idDest', $this->getDestino(true));
        Util::appendNode($ident, 'cMunFG', $municipio->getCodigo(true));
        Util::appendNode($ident, 'tpImp', $this->getFormato(true));
        Util::appendNode($ident, 'tpEmis', $this->getEmissao(true));
        Util::appendNode($ident, 'cDV', $this->getDigitoVerificador(true));
        Util::appendNode($ident, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($ident, 'finNFe', $this->getFinalidade(true));
        Util::appendNode($ident, 'indFinal', $this->getConsumidorFinal(true));
        Util::appendNode($ident, 'indPres', $this->getPresenca(true));
        Util::appendNode($ident, 'procEmi', 0); // emissão de NF-e com aplicativo do contribuinte
        Util::appendNode($ident, 'verProc', self::APP_VERSAO);
        if (!is_null($this->getDataMovimentacao())) {
            Util::appendNode($ident, 'dhSaiEnt', $this->getDataMovimentacao(true));
        }
        if ($this->getEmissao() != self::EMISSAO_NORMAL) {
            Util::appendNode($ident, 'dhCont', $this->getDataContingencia(true));
            Util::appendNode($ident, 'xJust', $this->getJustificativa(true));
        }
        $info->appendChild($ident);

        $emitente = $this->getEmitente()->getNode();
        $emitente = $dom->importNode($emitente, true);
        $info->appendChild($emitente);
        if ($this->getAmbiente() == self::AMBIENTE_HOMOLOGACAO && !is_null($this->getDestinatario())) {
            $this->getDestinatario()->setNome('NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL');
        }
        if (!is_null($this->getDestinatario())) {
            $destinatario = $this->getDestinatario()->getNode();
            $destinatario = $dom->importNode($destinatario, true);
            $info->appendChild($destinatario);
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
                if (!isset($tributos[$key])) {
                    $tributos[$key] = 0.00;
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
        $info_adic = $dom->createElement('infAdic');
        if (!is_null($this->getAdicionais())) {
            Util::appendNode($info_adic, 'infAdFisco', $this->getAdicionais(true));
        }
        // TODO: adicionar informações adicionais somente na NFC-e?
        $_complemento = Produto::addNodeInformacoes($tributos, $info_adic, 'infCpl');
        $this->getTotal()->setComplemento($_complemento);
        if (!is_null($this->getObservacoes())) {
            $_observacoes = $this->getObservacoes();
            foreach ($_observacoes as $_observacao) {
                $observacoes = $dom->createElement('obsCont');
                $campo = $dom->createAttribute('xCampo');
                $campo->value = $_observacao['campo'];
                $observacoes->appendChild($campo);
                Util::appendNode($observacoes, 'xTexto', $_observacao['valor']);
                $info_adic->appendChild($observacoes);
            }
        }
        if (!is_null($this->getInformacoes())) {
            $_informacoes = $this->getInformacoes();
            foreach ($_informacoes as $_informacao) {
                $informacoes = $dom->createElement('obsFisco');
                $campo = $dom->createAttribute('xCampo');
                $campo->value = $_informacao['campo'];
                $informacoes->appendChild($campo);
                Util::appendNode($informacoes, 'xTexto', $_informacao['valor']);
                $info_adic->appendChild($informacoes);
            }
        }
        $info->appendChild($info_adic);
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
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $_fields = $element->getElementsByTagName('infNFe');
        if ($_fields->length > 0) {
            $info = $_fields->item(0);
        } else {
            throw new \Exception('Tag "infNFe" não encontrada', 404);
        }
        $id = $info->getAttribute('Id');
        if (strlen($id) != 47) {
            throw new \Exception('Atributo "Id" inválido, encontrado: "'.$id.'"', 500);
        }
        $this->setID(substr($id, 3));
        $_fields = $info->getElementsByTagName('ide');
        if ($_fields->length > 0) {
            $ident = $_fields->item(0);
        } else {
            throw new \Exception('Tag "ide" não encontrada', 404);
        }
        $emitente = new Emitente();
        $emitente->getEndereco()->getMunicipio()->getEstado()->setCodigo(
            Util::loadNode(
                $ident,
                'cUF',
                'Tag "cUF" do campo "Codigo IBGE da UF" não encontrada'
            )
        );
        $this->setCodigo(
            Util::loadNode(
                $ident,
                'cNF',
                'Tag "cNF" do campo "Codigo" não encontrada'
            )
        );
        $this->setNatureza(
            Util::loadNode(
                $ident,
                'natOp',
                'Tag "natOp" do campo "Natureza" não encontrada'
            )
        );
        $this->setIndicador(
            Util::loadNode(
                $ident,
                'indPag',
                'Tag "indPag" do campo "Indicador" não encontrada'
            )
        );
        $this->setModelo(
            Util::loadNode(
                $ident,
                'mod',
                'Tag "mod" do campo "Modelo" não encontrada'
            )
        );
        $this->setSerie(
            Util::loadNode(
                $ident,
                'serie',
                'Tag "serie" do campo "Serie" não encontrada'
            )
        );
        $this->setNumero(
            Util::loadNode(
                $ident,
                'nNF',
                'Tag "nNF" do campo "Numero" não encontrada'
            )
        );
        $this->setDataEmissao(
            Util::loadNode(
                $ident,
                'dhEmi',
                'Tag "dhEmi" do campo "DataEmissao" não encontrada'
            )
        );
        $this->setTipo(
            Util::loadNode(
                $ident,
                'tpNF',
                'Tag "tpNF" do campo "Tipo" não encontrada'
            )
        );
        $this->setDestino(
            Util::loadNode(
                $ident,
                'idDest',
                'Tag "idDest" do campo "Destino" não encontrada'
            )
        );
        $emitente->getEndereco()->getMunicipio()->setCodigo(
            Util::loadNode(
                $ident,
                'cMunFG',
                'Tag "cMunFG" do campo "Codigo IBGE do município" não encontrada'
            )
        );
        $this->setDataMovimentacao(Util::loadNode($ident, 'dhSaiEnt'));
        $this->setFormato(
            Util::loadNode(
                $ident,
                'tpImp',
                'Tag "tpImp" do campo "Formato" não encontrada'
            )
        );
        $this->setEmissao(
            Util::loadNode(
                $ident,
                'tpEmis',
                'Tag "tpEmis" do campo "Emissao" não encontrada'
            )
        );
        $this->setDigitoVerificador(
            Util::loadNode(
                $ident,
                'cDV',
                'Tag "cDV" do campo "DigitoVerificador" não encontrada'
            )
        );
        $this->setAmbiente(
            Util::loadNode(
                $ident,
                'tpAmb',
                'Tag "tpAmb" do campo "Ambiente" não encontrada'
            )
        );
        $this->setFinalidade(
            Util::loadNode(
                $ident,
                'finNFe',
                'Tag "finNFe" do campo "Finalidade" não encontrada'
            )
        );
        $this->setConsumidorFinal(
            Util::loadNode(
                $ident,
                'indFinal',
                'Tag "indFinal" do campo "ConsumidorFinal" não encontrada'
            )
        );
        $this->setPresenca(
            Util::loadNode(
                $ident,
                'indPres',
                'Tag "indPres" do campo "Presenca" não encontrada'
            )
        );
        $this->setDataContingencia(Util::loadNode($ident, 'dhCont'));
        $this->setJustificativa(Util::loadNode($ident, 'xJust'));
        $emitente->loadNode(
            Util::findNode(
                $info,
                'emit',
                'Tag "emit" do objeto "Emitente" não encontrada'
            ),
            'emit'
        );
        $this->setEmitente($emitente);
        $_fields = $info->getElementsByTagName('dest');
        $destinatario = null;
        if ($_fields->length > 0) {
            $destinatario = new Destinatario();
            $destinatario->loadNode($_fields->item(0), 'dest');
        }
        $this->setDestinatario($destinatario);
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
        $_fields = $info->getElementsByTagName('total');
        if ($_fields->length > 0) {
            $total = new Total();
            $total->loadNode($_fields->item(0), 'total');
            $total->setComplemento(Util::loadNode($info, 'infCpl'));
        } else {
            throw new \Exception('Tag "total" do objeto "Total" não encontrada na Nota', 404);
        }
        $this->setTotal($total);
        $this->setAdicionais(Util::loadNode($info, 'infAdFisco'));
        $observacoes = array();
        $_items = $info->getElementsByTagName('obsCont');
        foreach ($_items as $_item) {
            $observacao = array(
                'campo' => $_item->getAttribute('xCampo'),
                'valor' => Util::loadNode(
                    $_item,
                    'xTexto',
                    'Tag "xTexto" do campo "Observação" não encontrada'
                )
            );
            $observacoes[] = $observacao;
        }
        $this->setObservacoes($observacoes);
        $informacoes = array();
        $_items = $info->getElementsByTagName('obsFisco');
        foreach ($_items as $_item) {
            $informacao = array(
                'campo' => $_item->getAttribute('xCampo'),
                'valor' => Util::loadNode(
                    $_item,
                    'xTexto',
                    'Tag "xTexto" do campo "Informação" não encontrada'
                )
            );
            $informacoes[] = $informacao;
        }
        $this->setInformacoes($informacoes);

        $_fields = $root->getElementsByTagName('protNFe');
        $protocolo = null;
        if ($_fields->length > 0) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($_fields->item(0), 'infProt');
        }
        $this->setProtocolo($protocolo);
        return $element;
    }

    /**
     * Carrega um arquivo XML e preenche a nota com as informações dele
     * @param  string $filename caminho do arquivo
     * @return DOMDocument      objeto do documento carregado
     */
    public function load($filename)
    {
        $dom = new \DOMDocument();
        if (!file_exists($filename)) {
            throw new \Exception('Arquivo XML "'.$filename.'" não encontrado', 404);
        }
        $dom->load($filename);
        $this->loadNode($dom->documentElement);
        return $dom;
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
        $xsd_path = __DIR__ . '/schema';
        if (is_null($this->getProtocolo())) {
            $xsd_file = $xsd_path . '/nfe_v3.10.xsd';
        } else {
            $xsd_file = $xsd_path . '/procNFe_v3.10.xsd';
        }
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

    /**
     * Adiciona o protocolo no XML da nota
     */
    public function addProtocolo($dom)
    {
        if (is_null($this->getProtocolo())) {
            throw new \Exception('O protocolo não foi informado na nota "'.$this->getID().'"', 404);
        }
        $notae = $dom->getElementsByTagName('NFe')->item(0);
        // Corrige xmlns:default
        $notae_xml = $dom->saveXML($notae);

        $element = $dom->createElement('nfeProc');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);
        $dom->removeChild($notae);
        // Corrige xmlns:default
        $notae = $dom->createElement('NFe', 0);

        $element->appendChild($notae);
        $info = $this->getProtocolo()->getNode();
        $info = $dom->importNode($info, true);
        $element->appendChild($info);
        $dom->appendChild($element);
        // Corrige xmlns:default
        $xml = $dom->saveXML();
        $xml = str_replace('<NFe>0</NFe>', $notae_xml, $xml);
        $dom->loadXML($xml);

        return $dom;
    }
}
