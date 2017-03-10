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
namespace NFe\Database;

use NFe\Common\Util;

class Estatico extends Banco
{

    private $ibpt;
    private $uf_codes;
    private $mun_codes;
    private $servicos;
    private $data_dir;

    public function __construct($estatico = array())
    {
        parent::__construct($estatico);
        $this->data_dir = __DIR__ . '/data';
        $this->load();
    }

    public function load()
    {
        $json = file_get_contents($this->data_dir . '/uf_ibge_code.json');
        $this->uf_codes = json_decode($json, true);
        if ($this->uf_codes === false || is_null($this->uf_codes)) {
            throw new \Exception('Falha ao carregar os códigos das unidades federadas', json_last_error());
        }
        $json = file_get_contents($this->data_dir . '/municipio_ibge_code.json');
        $this->mun_codes = json_decode($json, true);
        if ($this->mun_codes === false || is_null($this->mun_codes)) {
            throw new \Exception('Falha ao carregar os códigos dos municípios', json_last_error());
        }
        $json = file_get_contents($this->data_dir . '/servicos.json');
        $this->servicos = json_decode($json, true);
        if ($this->servicos === false || is_null($this->servicos)) {
            throw new \Exception('Falha ao carregar serviços da SEFAZ', json_last_error());
        }
    }

    public function getIBPT()
    {
        return $this->ibpt;
    }

    public function setIBPT($ibpt)
    {
        $this->ibpt = $ibpt;
        return $this;
    }

    /**
     * Obtém o código IBGE do estado
     */
    public function getCodigoEstado($uf)
    {
        if (!isset($this->uf_codes['estados'][strtoupper($uf)])) {
            throw new \Exception('Não foi encontrado o código do IBGE para o estado "'.$uf.'"', 404);
        }
        $codigo = $this->uf_codes['estados'][strtoupper($uf)];
        return intval($codigo);
    }

    /**
     * Obtém o código do orgão por estado
     */
    public function getCodigoOrgao($uf)
    {
        if (!isset($this->uf_codes['orgaos'][strtoupper($uf)])) {
            throw new \Exception('Não foi encontrado o código do orgão para o estado "'.$uf.'"', 404);
        }
        $codigo = $this->uf_codes['orgaos'][strtoupper($uf)];
        return intval($codigo);
    }

    /**
     * Obtém a aliquota do imposto de acordo com o tipo
     */
    public function getImpostoAliquota($ncm, $uf, $ex = null, $cnpj = null, $token = null)
    {
        return $this->getIBPT()->getImposto($cnpj, $token, $ncm, $uf, $ex);
    }

    /**
     * Obtém o código IBGE do município
     */
    public function getCodigoMunicipio($municipio, $uf)
    {
        if (!isset($this->mun_codes['municipios'][strtoupper($uf)])) {
            throw new \Exception('Não exite municípios para o estado "'.$uf.'"', 404);
        }
        $array = $this->mun_codes['municipios'][strtoupper($uf)];
        $elem = array('nome' => $municipio);
        $o = Util::binarySearch($elem, $array, function ($o1, $o2) {
            $n1 = Util::removeAccent($o1['nome']);
            $n2 = Util::removeAccent($o2['nome']);
            return strcasecmp($n1, $n2);
        });
        if ($o === false) {
            throw new \Exception('Não foi encontrado o código do IBGE para o município "'.
                $municipio.'" do estado "'.$uf.'"', 404);
        }
        return $o['codigo'];
    }

    /**
     * Obtém as notas pendentes de envio, em contingência e corrigidas após
     * rejeitadas
     */
    public function getNotasAbertas($inicio = null, $quantidade = null)
    {
        return array(); // TODO implementar
    }

    /**
     * Obtém as notas em processamento para consulta e possível protocolação
     */
    public function getNotasPendentes($inicio = null, $quantidade = null)
    {
        return array(); // TODO implementar
    }

    /**
     * Obtém as tarefas de inutilização, cancelamento e consulta de notas
     * pendentes que entraram em contingência
     */
    public function getNotasTarefas($inicio = null, $quantidade = null)
    {
        return array(); // TODO implementar
    }

    public function getInformacaoServico($emissao, $uf, $modelo = null, $ambiente = null)
    {
        switch ($emissao) {
            case '1':
                $emissao = 'normal';
                break;
            case '9':
                $emissao = 'contingencia';
                break;
        }
        switch ($modelo) {
            case '55':
                $modelo = 'nfe';
                break;
            case '65':
                $modelo = 'nfce';
                break;
        }
        if ($modelo == 'nfce') {
            $emissao = 'normal'; // NFCe envia contingência pelo webservice normal
        }
        if (!isset($this->servicos[$emissao])) {
            throw new \Exception('Falha ao obter o serviço da SEFAZ para o tipo de emissão "'.$emissao.'"', 404);
        }
        $array = $this->servicos[$emissao];
        if (!isset($array[strtoupper($uf)])) {
            throw new \Exception('Falha ao obter o serviço da SEFAZ para a UF "'.$uf.'"', 404);
        }
        $array = $array[strtoupper($uf)];
        if (!is_array($array)) {
            $array = $this->getInformacaoServico($emissao, $array);
        }
        $_modelos = array('nfe', 'nfce');
        foreach ($_modelos as $_modelo) {
            if (!isset($array[$_modelo])) {
                continue;
            }
            $node = $array[$_modelo];
            if (!is_array($node)) {
                $node = $this->getInformacaoServico($emissao, $node, $_modelo);
            }
            if (isset($node['base'])) {
                $base = $this->getInformacaoServico($emissao, $node['base'], $_modelo);
                $node = array_replace_recursive($node, $base);
            }
            $array[$_modelo] = $node;
        }
        if (!is_null($modelo)) {
            if (!isset($array[$modelo])) {
                throw new \Exception('Falha ao obter o serviço da SEFAZ para o modelo de nota "'.$modelo.'"', 404);
            }
            $array = $array[$modelo];
        }
        switch ($ambiente) {
            case '1':
                $ambiente = 'producao';
                break;
            case '2':
                $ambiente = 'homologacao';
                break;
        }
        if (!is_null($modelo) && !is_null($ambiente)) {
            if (!isset($array[$ambiente])) {
                throw new \Exception('Falha ao obter o serviço da SEFAZ para o ambiente "'.$ambiente.'"', 404);
            }
            $array = $array[$ambiente];
        }
        return $array;
    }

    public function toArray($recursive = false)
    {
        $estatico = parent::toArray($recursive);
        $estatico['ibpt'] = $this->getIBPT();
        return $estatico;
    }

    public function fromArray($estatico = array())
    {
        if ($estatico instanceof Estatico) {
            $estatico = $estatico->toArray();
        } elseif (!is_array($estatico)) {
            return $this;
        }
        parent::fromArray($estatico);
        if (!isset($estatico['ibpt']) || is_null($estatico['ibpt'])) {
            $this->setIBPT(new IBPT());
        } else {
            $this->setIBPT($estatico['ibpt']);
        }
        return $this;
    }
}
