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

/**
 * Classe base dos impostos
 */
abstract class Imposto implements Node
{

    /**
     * Tipo de imposto
     */
    const TIPO_IMPORTADO = 'importado';
    const TIPO_NACIONAL = 'nacional';
    const TIPO_ESTADUAL = 'estadual';
    const TIPO_MUNICIPAL = 'municipal';

    /**
     * Grupo do imposto
     */
    const GRUPO_ICMS = 'icms';
    const GRUPO_PIS = 'pis';
    const GRUPO_COFINS = 'cofins';
    const GRUPO_IPI = 'ipi';
    const GRUPO_II = 'ii';
    const GRUPO_PISST = 'pisst';
    const GRUPO_COFINSST = 'cofinsst';
    const GRUPO_ISSQN = 'issqn';
    const GRUPO_ICMSUFDEST = 'icmsufdest';

    private $tipo;
    private $grupo;
    private $tributacao;
    private $aliquota;
    private $base;

    public function __construct($imposto = array())
    {
        $this->fromArray($imposto);
    }

    /**
     * Tipo de imposto
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
     * Grupo do imposto
     */
    public function getGrupo($normalize = false)
    {
        if (!$normalize) {
            return $this->grupo;
        }
        switch ($this->grupo) {
            case self::GRUPO_ICMS:
                return 'ICMS';
            case self::GRUPO_PIS:
                return 'PIS';
            case self::GRUPO_COFINS:
                return 'COFINS';
            case self::GRUPO_IPI:
                return 'IPI';
            case self::GRUPO_II:
                return 'II';
            case self::GRUPO_PISST:
                return 'PISST';
            case self::GRUPO_COFINSST:
                return 'COFINSST';
            case self::GRUPO_ISSQN:
                return 'ISSQN';
            case self::GRUPO_ICMSUFDEST:
                return 'ICMSUFDest';
        }
        return $this->grupo;
    }

    public function setGrupo($grupo)
    {
        switch ($grupo) {
            case 'ICMS':
                $grupo = self::GRUPO_ICMS;
                break;
            case 'PIS':
                $grupo = self::GRUPO_PIS;
                break;
            case 'COFINS':
                $grupo = self::GRUPO_COFINS;
                break;
            case 'IPI':
                $grupo = self::GRUPO_IPI;
                break;
            case 'II':
                $grupo = self::GRUPO_II;
                break;
            case 'PISST':
                $grupo = self::GRUPO_PISST;
                break;
            case 'COFINSST':
                $grupo = self::GRUPO_COFINSST;
                break;
            case 'ISSQN':
                $grupo = self::GRUPO_ISSQN;
                break;
            case 'ICMSUFDest':
                $grupo = self::GRUPO_ICMSUFDEST;
                break;
        }
        $this->grupo = $grupo;
        return $this;
    }

    /**
     * Código da situação tributária
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return $this->tributacao;
        }
        return $this->tributacao;
    }

    public function setTributacao($tributacao)
    {
        $this->tributacao = $tributacao;
        return $this;
    }

    /**
     * Porcentagem do imposto
     */
    public function getAliquota($normalize = false)
    {
        if (!$normalize) {
            return $this->aliquota;
        }
        return Util::toFloat($this->aliquota);
    }

    public function setAliquota($aliquota)
    {
        $this->aliquota = $aliquota;
        return $this;
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return $this->base;
        }
        return Util::toCurrency($this->base);
    }

    public function setBase($base)
    {
        $this->base = $base;
        return $this;
    }

    /**
     * Calcula o valor do imposto com base na aliquota e valor base
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return ($this->getBase() * $this->getAliquota()) / 100.0;
        }
        return Util::toCurrency($this->getValor());
    }

    /**
     * Obtém o valor total do imposto
     */
    public function getTotal($normalize = false)
    {
        return $this->getValor($normalize);
    }

    public function toArray($recursive = false)
    {
        $imposto = array();
        $imposto['tipo'] = $this->getTipo();
        $imposto['grupo'] = $this->getGrupo();
        $imposto['tributacao'] = $this->getTributacao();
        $imposto['aliquota'] = $this->getAliquota();
        $imposto['base'] = $this->getBase();
        $imposto['valor'] = $this->getValor();
        return $imposto;
    }

    public function fromArray($imposto = array())
    {
        if ($imposto instanceof Imposto) {
            $imposto = $imposto->toArray();
        } elseif (!is_array($imposto)) {
            return $this;
        }
        if (isset($imposto['tipo'])) {
            $this->setTipo($imposto['tipo']);
        } else {
            $this->setTipo(null);
        }
        if (isset($imposto['grupo'])) {
            $this->setGrupo($imposto['grupo']);
        } else {
            $this->setGrupo(null);
        }
        if (isset($imposto['tributacao'])) {
            $this->setTributacao($imposto['tributacao']);
        } else {
            $this->setTributacao(null);
        }
        if (isset($imposto['aliquota'])) {
            $this->setAliquota($imposto['aliquota']);
        } else {
            $this->setAliquota(null);
        }
        if (isset($imposto['base'])) {
            $this->setBase($imposto['base']);
        } else {
            $this->setBase(null);
        }
        return $this;
    }

    public static function criaPeloNome($nome, $quantitativo = false)
    {
        switch ($nome) {
            /* Grupo COFINS */
            case 'COFINSAliq':
                $imposto = new Imposto\COFINS\Aliquota();
                break;
            case 'COFINSOutr':
                $imposto = new Imposto\COFINS\Generico();
                break;
            case 'COFINSNT':
                $imposto = new Imposto\COFINS\Isento();
                break;
            case 'COFINSQtde':
                $imposto = new Imposto\COFINS\Quantidade();
                break;
            /* Grupo COFINSST */
            case 'COFINSST':
                if ($quantitativo) {
                    $imposto = new Imposto\COFINS\ST\Quantidade();
                } else {
                    $imposto = new Imposto\COFINS\ST\Aliquota();
                }
                break;
            /* Grupo ICMS */
            case 'ICMS60':
                $imposto = new Imposto\ICMS\Cobrado();
                break;
            case 'ICMS10':
                $imposto = new Imposto\ICMS\Cobranca();
                break;
            case 'ICMS51':
                $imposto = new Imposto\ICMS\Diferido();
                break;
            case 'ICMS90':
                $imposto = new Imposto\ICMS\Generico();
                break;
            case 'ICMS00':
                $imposto = new Imposto\ICMS\Integral();
                break;
            case 'ICMS40':
                $imposto = new Imposto\ICMS\Isento();
                break;
            case 'ICMS70':
                $imposto = new Imposto\ICMS\Mista();
                break;
            case 'IMCS30':
                $imposto = new Imposto\ICMS\Parcial();
                break;
            case 'ICMSPart':
                $imposto = new Imposto\ICMS\Partilha();
                break;
            case 'ICMS20':
                $imposto = new Imposto\ICMS\Reducao();
                break;
            case 'ICMSST':
                $imposto = new Imposto\ICMS\Substituto();
                break;
            /* Grupo ICMS Simples */
            case 'ICMSSN500':
                $imposto = new Imposto\ICMS\Simples\Cobrado();
                break;
            case 'ICMSSN201':
                $imposto = new Imposto\ICMS\Simples\Cobranca();
                break;
            case 'ICMSSN900':
                $imposto = new Imposto\ICMS\Simples\Generico();
                break;
            case 'ICMSSN102':
                $imposto = new Imposto\ICMS\Simples\Isento();
                break;
            case 'ICMSSN101':
                $imposto = new Imposto\ICMS\Simples\Normal();
                break;
            case 'IMCSSN202':
                $imposto = new Imposto\ICMS\Simples\Parcial();
                break;
            /* Grupo IPI */
            case 'IPITrib':
                if ($quantitativo) {
                    $imposto = new Imposto\IPI\Quantidade();
                } else {
                    $imposto = new Imposto\IPI\Aliquota();
                }
                break;
            case 'IPINT':
                $imposto = new Imposto\IPI\Isento();
                break;
            /* Grupo PIS */
            case 'PISAliq':
                $imposto = new Imposto\PIS\Aliquota();
                break;
            case 'PISOutr':
                $imposto = new Imposto\PIS\Generico();
                break;
            case 'PISNT':
                $imposto = new Imposto\PIS\Isento();
                break;
            case 'PISQtde':
                $imposto = new Imposto\PIS\Quantidade();
                break;
            /* Grupo PISST */
            case 'PISST':
                if ($quantitativo) {
                    $imposto = new Imposto\PIS\ST\Quantidade();
                } else {
                    $imposto = new Imposto\PIS\ST\Aliquota();
                }
                break;
            /* Grupo II básico */
            case 'II':
                $imposto = new Imposto\II();
                break;
            /* Grupo IPI básico */
            case 'IPI':
                $imposto = new Imposto\IPI();
                break;
            default:
                return false;
        }
        return $imposto;
    }

    public static function loadImposto($element, $grupo = null)
    {
        $quantitativo = false;
        switch ($element->nodeName) {
            /* Grupo COFINSST */
            case 'COFINSST':
                $_fields = $element->getElementsByTagName('pCOFINS');
                $quantitativo = $_fields->length == 0;
                break;
            /* Grupo IPI */
            case 'IPITrib':
                $_fields = $element->getElementsByTagName('pIPI');
                $quantitativo = $_fields->length == 0;
                break;
            /* Grupo PISST */
            case 'PISST':
                $_fields = $element->getElementsByTagName('pPIS');
                $quantitativo = $_fields->length == 0;
                break;
        }
        $imposto = self::criaPeloNome($element->nodeName, $quantitativo);
        if ($imposto === false) {
            return false;
        }
        $imposto->loadNode($element, $element->nodeName);
        return $imposto;
    }
}
