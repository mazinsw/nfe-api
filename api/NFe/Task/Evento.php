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

class Evento extends Retorno
{

    const VERSAO = '1.00';

    const TIPO_CANCELAMENTO = '110111';
    const TAG_RETORNO = 'retEvento';
    const TAG_RETORNO_ENVIO = 'retEnvEvento';

    private $id;
    private $orgao;
    private $identificador;
    private $chave;
    private $data;
    private $tipo;
    private $sequencia;
    private $descricao;
    private $numero;
    private $justificativa;
    private $email;
    private $modelo;
    private $informacao;

    public function __construct($evento = array())
    {
        parent::__construct($evento);
    }

    /**
     * Identificador da TAG a ser assinada, a regra de formação do Id é: "ID" +
     * tpEvento +  chave da NF-e + nSeqEvento
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

    /**
     * Código do órgão de recepção do Evento. Utilizar a Tabela do IBGE
     * extendida, utilizar 91 para identificar o Ambiente Nacional
     */
    public function getOrgao($normalize = false)
    {
        if (!$normalize || is_numeric($this->orgao)) {
            return $this->orgao;
        }

        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        return $db->getCodigoOrgao($this->orgao);
    }

    public function setOrgao($orgao)
    {
        $this->orgao = $orgao;
        return $this;
    }

    /**
     * Identificação do  autor do evento
     */
    public function getIdentificador($normalize = false)
    {
        if (!$normalize) {
            return $this->identificador;
        }
        return $this->identificador;
    }

    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
        return $this;
    }

    /**
     * Chave de Acesso da NF-e vinculada ao evento
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
     * Data e Hora do Evento, formato UTC (AAAA-MM-DDThh:mm:ssTZD, onde TZD =
     * +hh:mm ou -hh:mm)
     */
    public function getData($normalize = false)
    {
        if (!$normalize) {
            return $this->data;
        }
        return Util::toDateTime($this->data);
    }

    public function setData($data)
    {
        if (!is_numeric($data)) {
            $data = strtotime($data);
        }
        $this->data = $data;
        return $this;
    }

    /**
     * Tipo do Evento
     */
    public function getTipo($normalize = false)
    {
        if (!$normalize) {
            return $this->tipo;
        }
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Seqüencial do evento para o mesmo tipo de evento.  Para maioria dos
     * eventos será 1, nos casos em que possa existir mais de um evento, como é
     * o caso da carta de correção, o autor do evento deve numerar de forma
     * seqüencial.
     */
    public function getSequencia($normalize = false)
    {
        if (!$normalize) {
            return $this->sequencia;
        }
        return $this->sequencia;
    }

    public function setSequencia($sequencia)
    {
        $this->sequencia = $sequencia;
        return $this;
    }

    /**
     * Descrição do Evento
     */
    public function getDescricao($normalize = false)
    {
        if (!$normalize) {
            return $this->descricao;
        }
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Número do Protocolo de Status da NF-e. 1 posição (1 – Secretaria de
     * Fazenda Estadual 2 – Receita Federal); 2 posições ano; 10 seqüencial no
     * ano.
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
     * Justificativa do cancelamento
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

    /**
     * email do destinatário
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

    /**
     * Resposta de informação do evento
     */
    public function getInformacao()
    {
        return $this->informacao;
    }

    public function setInformacao($informacao)
    {
        $this->informacao = $informacao;
        return $this;
    }

    /**
     * Informa se a identificação é um CNPJ
     */
    public function isCNPJ()
    {
        return strlen($this->getIdentificador()) == 14;
    }

    /**
     * Informa se o lote já foi processado e já tem um protocolo
     */
    public function isProcessado()
    {
        return $this->getStatus() == '128';
    }

    /**
     * Informa se a nota foi cancelada com sucesso
     */
    public function isCancelado()
    {
        return in_array($this->getStatus(), array('135', '155'));
    }

    public function toArray($recursive = false)
    {
        $evento = parent::toArray($recursive);
        $evento['id'] = $this->getID();
        $evento['orgao'] = $this->getOrgao();
        $evento['identificador'] = $this->getIdentificador();
        $evento['chave'] = $this->getChave();
        $evento['data'] = $this->getData();
        $evento['tipo'] = $this->getTipo();
        $evento['sequencia'] = $this->getSequencia();
        $evento['descricao'] = $this->getDescricao();
        $evento['numero'] = $this->getNumero();
        $evento['justificativa'] = $this->getJustificativa();
        $evento['email'] = $this->getEmail();
        $evento['modelo'] = $this->getModelo();
        $evento['informacao'] = $this->getInformacao();
        return $evento;
    }

    public function fromArray($evento = array())
    {
        if ($evento instanceof Evento) {
            $evento = $evento->toArray();
        } elseif (!is_array($evento)) {
            return $this;
        }
        parent::fromArray($evento);
        if (isset($evento['id'])) {
            $this->setID($evento['id']);
        } else {
            $this->setID(null);
        }
        if (isset($evento['orgao'])) {
            $this->setOrgao($evento['orgao']);
        } else {
            $this->setOrgao(null);
        }
        if (isset($evento['identificador'])) {
            $this->setIdentificador($evento['identificador']);
        } else {
            $this->setIdentificador(null);
        }
        if (isset($evento['chave'])) {
            $this->setChave($evento['chave']);
        } else {
            $this->setChave(null);
        }
        if (isset($evento['data'])) {
            $this->setData($evento['data']);
        } else {
            $this->setData(null);
        }
        if (!isset($evento['tipo']) || is_null($evento['tipo'])) {
            $this->setTipo(self::TIPO_CANCELAMENTO);
        } else {
            $this->setTipo($evento['tipo']);
        }
        if (!isset($evento['sequencia']) || is_null($evento['sequencia'])) {
            $this->setSequencia(1);
        } else {
            $this->setSequencia($evento['sequencia']);
        }
        if (!isset($evento['descricao']) || is_null($evento['descricao'])) {
            $this->setDescricao('Cancelamento');
        } else {
            $this->setDescricao($evento['descricao']);
        }
        if (isset($evento['numero'])) {
            $this->setNumero($evento['numero']);
        } else {
            $this->setNumero(null);
        }
        if (isset($evento['justificativa'])) {
            $this->setJustificativa($evento['justificativa']);
        } else {
            $this->setJustificativa(null);
        }
        if (isset($evento['email'])) {
            $this->setEmail($evento['email']);
        } else {
            $this->setEmail(null);
        }
        if (isset($evento['modelo'])) {
            $this->setModelo($evento['modelo']);
        } else {
            $this->setModelo(null);
        }
        if (isset($evento['informacao'])) {
            $this->setInformacao($evento['informacao']);
        } else {
            $this->setInformacao(null);
        }
        return $this;
    }

    /**
     * Gera o ID, a regra de formação do Id é: "ID" +
     * tpEvento +  chave da NF-e + nSeqEvento
     */
    public function gerarID()
    {
        $id = sprintf(
            '%s%s%02d',
            $this->getTipo(true),
            $this->getChave(true),
            $this->getSequencia(true)
        );
        return $id;
    }

    public function getNode($name = null)
    {
        $this->setID($this->gerarID());

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'evento':$name);
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);

        $info = $dom->createElement('infEvento');
        $dom = $element->ownerDocument;
        $id = $dom->createAttribute('Id');
        $id->value = $this->getID(true);
        $info->appendChild($id);
        
        Util::appendNode($info, 'cOrgao', $this->getOrgao(true));
        Util::appendNode($info, 'tpAmb', $this->getAmbiente(true));
        if ($this->isCNPJ()) {
            Util::appendNode($info, 'CNPJ', $this->getIdentificador(true));
        } else {
            Util::appendNode($info, 'CPF', $this->getIdentificador(true));
        }
        Util::appendNode($info, 'chNFe', $this->getChave(true));
        Util::appendNode($info, 'dhEvento', $this->getData(true));
        Util::appendNode($info, 'tpEvento', $this->getTipo(true));
        Util::appendNode($info, 'nSeqEvento', $this->getSequencia(true));
        Util::appendNode($info, 'verEvento', self::VERSAO);

        $detalhes = $dom->createElement('detEvento');
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $detalhes->appendChild($versao);

        Util::appendNode($detalhes, 'descEvento', $this->getDescricao(true));
        Util::appendNode($detalhes, 'nProt', $this->getNumero(true));
        Util::appendNode($detalhes, 'xJust', $this->getJustificativa(true));
        $info->appendChild($detalhes);

        $element->appendChild($info);
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $root = $element;
        $element = Util::findNode($element, 'evento');
        $name = is_null($name)?'infEvento':$name;
        $element = Util::findNode($element, $name);
        $this->setOrgao(
            Util::loadNode(
                $element,
                'cOrgao',
                'Tag "cOrgao" não encontrada no Evento'
            )
        );
        $this->setAmbiente(
            Util::loadNode(
                $element,
                'tpAmb',
                'Tag "tpAmb" não encontrada no Evento'
            )
        );
        if (Util::nodeExists($element, 'CNPJ')) {
            $this->setIdentificador(
                Util::loadNode(
                    $element,
                    'CNPJ',
                    'Tag "CNPJ" não encontrada no Evento'
                )
            );
        } else {
            $this->setIdentificador(
                Util::loadNode(
                    $element,
                    'CPF',
                    'Tag "CPF" não encontrada no Evento'
                )
            );
        }
        $this->setChave(
            Util::loadNode(
                $element,
                'chNFe',
                'Tag "chNFe" não encontrada no Evento'
            )
        );
        $this->setData(
            Util::loadNode(
                $element,
                'dhEvento',
                'Tag "dhEvento" não encontrada no Evento'
            )
        );
        $this->setTipo(
            Util::loadNode(
                $element,
                'tpEvento',
                'Tag "tpEvento" não encontrada no Evento'
            )
        );
        $this->setSequencia(
            Util::loadNode(
                $element,
                'nSeqEvento',
                'Tag "nSeqEvento" não encontrada no Evento'
            )
        );

        $detalhes = Util::findNode($element, 'detEvento');
        $this->setDescricao(
            Util::loadNode(
                $detalhes,
                'descEvento',
                'Tag "descEvento" não encontrada no Evento'
            )
        );
        $this->setNumero(
            Util::loadNode(
                $detalhes,
                'nProt',
                'Tag "nProt" não encontrada no Evento'
            )
        );
        $this->setJustificativa(
            Util::loadNode(
                $detalhes,
                'xJust',
                'Tag "xJust" não encontrada no Evento'
            )
        );
        $informacao = null;
        if (Util::nodeExists($root, 'procEventoNFe')) {
            $informacao = $this->loadResponse($root);
        }
        $this->setInformacao($informacao);
        return $element;
    }

    public function loadResponse($resp)
    {
        $retorno = new Evento();
        $retorno->loadReturnNode($resp);
        $this->setInformacao($retorno);
        return $retorno;
    }

    public function loadStatusNode($element, $name = null)
    {
        $name = is_null($name)?self::TAG_RETORNO_ENVIO:$name;
        $element = parent::loadNode($element, $name);
        $this->setOrgao(
            Util::loadNode(
                $element,
                'cOrgao',
                'Tag "cOrgao" do campo "Orgao" não encontrada'
            )
        );
        return $element;
    }

    public function getReturnNode()
    {
        $outros = parent::getNode('infEvento');
        $element = $this->getNode(self::TAG_RETORNO);
        $dom = $element->ownerDocument;
        $element->removeAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns');
        $info = $dom->getElementsByTagName('infEvento')->item(0);
        $info->removeAttribute('Id');
        $removeTags = array('detEvento', 'verEvento', 'dhEvento', 'CNPJ', 'CPF', 'cOrgao');
        foreach ($removeTags as $key) {
            $_fields = $info->getElementsByTagName($key);
            if ($_fields->length == 0) {
                continue;
            }
            $node = $_fields->item(0);
            $info->removeChild($node);
        }
        $chave = $info->getElementsByTagName('chNFe')->item(0);
        foreach ($outros->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $info->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                continue;
            }
            $info->insertBefore($node, $chave);
        }
        $status = $info->getElementsByTagName('cStat')->item(0);
        Util::appendNode($info, 'cOrgao', $this->getOrgao(true), $status);
        $sequencia = $info->getElementsByTagName('nSeqEvento')->item(0);
        Util::appendNode($info, 'xEvento', $this->getDescricao(true), $sequencia);
        if (!is_null($this->getIdentificador())) {
            if ($this->isCNPJ()) {
                Util::appendNode($info, 'CNPJDest', $this->getIdentificador(true));
            } else {
                Util::appendNode($info, 'CPFDest', $this->getIdentificador(true));
            }
        }
        if (!is_null($this->getEmail())) {
            Util::appendNode($info, 'emailDest', $this->getEmail(true));
        }
        Util::appendNode($info, 'dhRegEvento', $this->getData(true));
        Util::appendNode($info, 'nProt', $this->getNumero(true));
        return $element;
    }

    public function loadReturnNode($element, $name = null)
    {
        $element = Util::findNode($element, Evento::TAG_RETORNO);
        $name = is_null($name)?'infEvento':$name;
        $element = parent::loadNode($element, $name);
        $this->setOrgao(
            Util::loadNode(
                $element,
                'cOrgao',
                'Tag "cOrgao" do campo "Orgao" não encontrada'
            )
        );
        $this->setChave(Util::loadNode($element, 'chNFe'));
        $this->setTipo(Util::loadNode($element, 'tpEvento'));
        $this->setDescricao(Util::loadNode($element, 'xEvento'));
        $this->setSequencia(Util::loadNode($element, 'nSeqEvento'));
        if ($element->getElementsByTagName('CNPJDest')->length > 0) {
            $this->setIdentificador(Util::loadNode($element, 'CNPJDest'));
        } else {
            $this->setIdentificador(Util::loadNode($element, 'CPFDest'));
        }
        $this->setEmail(Util::loadNode($element, 'emailDest'));
        $this->setData(
            Util::loadNode(
                $element,
                'dhRegEvento',
                'Tag "dhRegEvento" do campo "Data" não encontrada'
            )
        );
        $this->setNumero(Util::loadNode($element, 'nProt'));
        return $element;
    }

    private function getConteudo($dom)
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $dob = new \DOMDocument('1.0', 'UTF-8');
        $envio = $dob->createElement('envEvento');
        $envio->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dob->createAttribute('versao');
        $versao->value = self::VERSAO;
        $envio->appendChild($versao);
        Util::appendNode($envio, 'idLote', self::genLote());
        // Corrige xmlns:default
        // $data = $dob->importNode($dom->documentElement, true);
        // $envio->appendChild($data);
        Util::appendNode($envio, 'evento', 0);
        $dob->appendChild($envio);
        // Corrige xmlns:default
        // return $dob;
        $xml = $dob->saveXML($dob->documentElement);
        return str_replace('<evento>0</evento>', $dom->saveXML($dom->documentElement), $xml);
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_EVENTO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setConteudo($this->getConteudo($dom));
        $resp = $envio->envia();
        $this->loadStatusNode($resp);
        if (!$this->isProcessado()) {
            throw new \Exception($this->getMotivo(), $this->getStatus());
        }
        return $this->loadResponse($resp);
    }

    /**
     * Adiciona a informação no XML do evento
     */
    public function addInformacao($dom)
    {
        if (is_null($this->getInformacao())) {
            throw new \Exception('A informação não foi informado no evento "'.$this->getID().'"', 404);
        }
        $evento = $dom->getElementsByTagName('evento')->item(0);
        // Corrige xmlns:default
        $evento_xml = $dom->saveXML($evento);

        $element = $dom->createElement('procEventoNFe');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);
        $dom->removeChild($evento);
        // Corrige xmlns:default
        $evento = $dom->createElement('evento', 0);

        $element->appendChild($evento);
        $info = $this->getInformacao()->getReturnNode();
        $info = $dom->importNode($info, true);
        $element->appendChild($info);
        $dom->appendChild($element);
        // Corrige xmlns:default
        $xml = $dom->saveXML();
        $xml = str_replace('<evento>0</evento>', $evento_xml, $xml);
        $dom->loadXML($xml);

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
        $adapter->sign($dom, 'infEvento');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(__DIR__) . '/Core/schema';
        $xsd_file = $xsd_path . '/cancelamento/eventoCancNFe_v1.00.xsd';
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
