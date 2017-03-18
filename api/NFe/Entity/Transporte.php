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
use NFe\Entity\Transporte\Veiculo;
use NFe\Entity\Transporte\Tributo;
use NFe\Entity\Transporte\Transportador;

/**
 * Dados dos transportes da NF-e
 */
class Transporte implements Node
{

    /**
     * Modalidade do frete
     * 0- Por conta do emitente;
     * 1- Por conta do
     * destinatário/remetente;
     * 2- Por conta de terceiros;
     * 9- Sem frete (v2.0)
     */
    const FRETE_EMITENTE = 'emitente';
    const FRETE_DESTINATARIO = 'destinatario';
    const FRETE_TERCEIROS = 'terceiros';
    const FRETE_NENHUM = 'nenhum';

    private $frete;
    private $transportador;
    private $retencao;
    private $veiculo;
    private $reboque;
    private $vagao;
    private $balsa;
    private $volumes;

    public function __construct($transporte = array())
    {
        $this->fromArray($transporte);
    }

    /**
     * Modalidade do frete
     * 0- Por conta do emitente;
     * 1- Por conta do
     * destinatário/remetente;
     * 2- Por conta de terceiros;
     * 9- Sem frete (v2.0)
     */
    public function getFrete($normalize = false)
    {
        if (!$normalize) {
            return $this->frete;
        }
        switch ($this->frete) {
            case self::FRETE_EMITENTE:
                return '0';
            case self::FRETE_DESTINATARIO:
                return '1';
            case self::FRETE_TERCEIROS:
                return '2';
            case self::FRETE_NENHUM:
                return '9';
        }
        return $this->frete;
    }

    public function setFrete($frete)
    {
        switch ($frete) {
            case '0':
                $frete = self::FRETE_EMITENTE;
                break;
            case '1':
                $frete = self::FRETE_DESTINATARIO;
                break;
            case '2':
                $frete = self::FRETE_TERCEIROS;
                break;
            case '9':
                $frete = self::FRETE_NENHUM;
                break;
        }
        $this->frete = $frete;
        return $this;
    }

    /**
     * Dados da transportadora
     */
    public function getTransportador()
    {
        return $this->transportador;
    }

    public function setTransportador($transportador)
    {
        $this->transportador = $transportador;
        return $this;
    }

    /**
     * Dados da retenção  ICMS do Transporte
     */
    public function getRetencao()
    {
        return $this->retencao;
    }

    public function setRetencao($retencao)
    {
        $this->retencao = $retencao;
        return $this;
    }

    /**
     * Dados do veículo
     */
    public function getVeiculo()
    {
        return $this->veiculo;
    }

    public function setVeiculo($veiculo)
    {
        $this->veiculo = $veiculo;
        return $this;
    }

    /**
     * Dados do reboque/Dolly (v2.0)
     */
    public function getReboque()
    {
        return $this->reboque;
    }

    public function setReboque($reboque)
    {
        $this->reboque = $reboque;
        return $this;
    }

    /**
     * Identificação do vagão (v2.0)
     */
    public function getVagao($normalize = false)
    {
        if (!$normalize) {
            return $this->vagao;
        }
        return $this->vagao;
    }

    public function setVagao($vagao)
    {
        $this->vagao = $vagao;
        return $this;
    }

    /**
     * Identificação da balsa (v2.0)
     */
    public function getBalsa($normalize = false)
    {
        if (!$normalize) {
            return $this->balsa;
        }
        return $this->balsa;
    }

    public function setBalsa($balsa)
    {
        $this->balsa = $balsa;
        return $this;
    }

    /**
     * Dados dos volumes
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    public function setVolumes($volumes)
    {
        $this->volumes = $volumes;
        return $this;
    }

    public function addVolume($volume)
    {
        $this->volumes[] = $volume;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $transporte = array();
        $transporte['frete'] = $this->getFrete();
        if (!is_null($this->getTransportador()) && $recursive) {
            $transporte['transportador'] = $this->getTransportador()->toArray($recursive);
        } else {
            $transporte['transportador'] = $this->getTransportador();
        }
        if (!is_null($this->getRetencao()) && $recursive) {
            $transporte['retencao'] = $this->getRetencao()->toArray($recursive);
        } else {
            $transporte['retencao'] = $this->getRetencao();
        }
        if (!is_null($this->getVeiculo()) && $recursive) {
            $transporte['veiculo'] = $this->getVeiculo()->toArray($recursive);
        } else {
            $transporte['veiculo'] = $this->getVeiculo();
        }
        if (!is_null($this->getReboque()) && $recursive) {
            $transporte['reboque'] = $this->getReboque()->toArray($recursive);
        } else {
            $transporte['reboque'] = $this->getReboque();
        }
        $transporte['vagao'] = $this->getVagao();
        $transporte['balsa'] = $this->getBalsa();
        if ($recursive) {
            $volumes = array();
            $_volumes = $this->getVolumes();
            foreach ($_volumes as $_volume) {
                $volumes[] = $_volume->toArray($recursive);
            }
            $transporte['volumes'] = $volumes;
        } else {
            $transporte['volumes'] = $this->getVolumes();
        }
        return $transporte;
    }

    public function fromArray($transporte = array())
    {
        if ($transporte instanceof Transporte) {
            $transporte = $transporte->toArray();
        } elseif (!is_array($transporte)) {
            return $this;
        }
        if (!isset($transporte['frete']) || is_null($transporte['frete'])) {
            $this->setFrete(self::FRETE_NENHUM);
        } else {
            $this->setFrete($transporte['frete']);
        }
        if (!isset($transporte['transportador']) || is_null($transporte['transportador'])) {
            $this->setTransportador(new Transportador());
        } else {
            $this->setTransportador($transporte['transportador']);
        }
        if (!isset($transporte['retencao']) || is_null($transporte['retencao'])) {
            $this->setRetencao(new Tributo());
        } else {
            $this->setRetencao($transporte['retencao']);
        }
        if (!isset($transporte['veiculo']) || is_null($transporte['veiculo'])) {
            $this->setVeiculo(new Veiculo());
        } else {
            $this->setVeiculo($transporte['veiculo']);
        }
        if (!isset($transporte['reboque']) || is_null($transporte['reboque'])) {
            $this->setReboque(new Veiculo());
        } else {
            $this->setReboque($transporte['reboque']);
        }
        if (isset($transporte['vagao'])) {
            $this->setVagao($transporte['vagao']);
        } else {
            $this->setVagao(null);
        }
        if (isset($transporte['balsa'])) {
            $this->setBalsa($transporte['balsa']);
        } else {
            $this->setBalsa(null);
        }
        if (!isset($transporte['volumes']) || is_null($transporte['volumes'])) {
            $this->setVolumes(array());
        } else {
            $this->setVolumes($transporte['volumes']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'transp':$name);
        Util::appendNode($element, 'modFrete', $this->getFrete(true));
        if ($this->getFrete() == self::FRETE_NENHUM) {
            return $element;
        }
        if (!is_null($this->getTransportador())) {
            $transportador = $this->getTransportador()->getNode();
            $transportador = $dom->importNode($transportador, true);
            $element->appendChild($transportador);
        }
        if (!is_null($this->getRetencao())) {
            $retencao = $this->getRetencao()->getNode();
            $retencao = $dom->importNode($retencao, true);
            $element->appendChild($retencao);
        }
        if (!is_null($this->getVeiculo())) {
            $veiculo = $this->getVeiculo()->getNode('veicTransp');
            $veiculo = $dom->importNode($veiculo, true);
            $element->appendChild($veiculo);
        }
        if (!is_null($this->getReboque())) {
            $reboque = $this->getReboque()->getNode('reboque');
            $reboque = $dom->importNode($reboque, true);
            $element->appendChild($reboque);
        }
        if (!is_null($this->getVagao())) {
            Util::appendNode($element, 'vagao', $this->getVagao(true));
        }
        if (!is_null($this->getBalsa())) {
            Util::appendNode($element, 'balsa', $this->getBalsa(true));
        }
        if (!is_null($this->getVolumes())) {
            $_volumes = $this->getVolumes();
            foreach ($_volumes as $_volume) {
                $volume = $_volume->getNode();
                $volume = $dom->importNode($volume, true);
                $element->appendChild($volume);
            }
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'transp':$name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setFrete(
            Util::loadNode(
                $element,
                'modFrete',
                'Tag "modFrete" do campo "Frete" não encontrada'
            )
        );
        $_fields = $element->getElementsByTagName('transporta');
        $transportador = null;
        if ($_fields->length > 0) {
            $transportador = new Transportador();
            $transportador->loadNode($_fields->item(0), 'transporta');
        }
        $this->setTransportador($transportador);
        $_fields = $element->getElementsByTagName('retTransp');
        $retencao = null;
        if ($_fields->length > 0) {
            $retencao = new Tributo();
            $retencao->loadNode($_fields->item(0), 'retTransp');
        }
        $this->setRetencao($retencao);
        $_fields = $element->getElementsByTagName('veicTransp');
        $veiculo = null;
        if ($_fields->length > 0) {
            $veiculo = new Veiculo();
            $veiculo->loadNode($_fields->item(0), 'veicTransp');
        }
        $this->setVeiculo($veiculo);
        $_fields = $element->getElementsByTagName('reboque');
        $reboque = null;
        if ($_fields->length > 0) {
            $reboque = new Veiculo();
            $reboque->loadNode($_fields->item(0), 'reboque');
        }
        $this->setReboque($reboque);
        $this->setVagao(Util::loadNode($element, 'vagao'));
        $this->setBalsa(Util::loadNode($element, 'balsa'));
        $volumes = array();
        $_fields = $element->getElementsByTagName('vol');
        foreach ($_fields as $_item) {
            $volume = new Volume();
            $volume->loadNode($_item, 'vol');
            $volumes[] = $volume;
        }
        $this->setVolumes($volumes);
        return $element;
    }
}
