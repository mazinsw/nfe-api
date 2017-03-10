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

use NFe\Core\SEFAZ;

/**
 * Município de um endereço
 */
class Municipio
{

    private $estado;
    private $codigo;
    private $nome;

    public function __construct($municipio = array())
    {
        $this->fromArray($municipio);
    }

    /**
     * Estado do município
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * Código do município (utilizar a tabela do IBGE), informar 9999999 para
     * operações com o exterior.
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Nome do munícipio
     */
    public function getNome($normalize = false)
    {
        if (!$normalize) {
            return $this->nome;
        }
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Verifica se o código do municipio foi preenchido,
     * caso contrário realiza uma busca usando o nome e a UF e preenche
     * @return void
     */
    public function checkCodigos()
    {
        if (is_numeric($this->getCodigo())) {
            return;
        }
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $this->setCodigo($db->getCodigoMunicipio(
            $this->getNome(),
            $this->getEstado()->getUF()
        ));
    }

    public function toArray($recursive = false)
    {
        $municipio = array();
        if (!is_null($this->getEstado()) && $recursive) {
            $municipio['estado'] = $this->getEstado()->toArray($recursive);
        } else {
            $municipio['estado'] = $this->getEstado();
        }
        $municipio['codigo'] = $this->getCodigo();
        $municipio['nome'] = $this->getNome();
        return $municipio;
    }

    public function fromArray($municipio = array())
    {
        if ($municipio instanceof Municipio) {
            $municipio = $municipio->toArray();
        } elseif (!is_array($municipio)) {
            return $this;
        }
        if (!isset($municipio['estado']) || is_null($municipio['estado'])) {
            $this->setEstado(new Estado());
        } else {
            $this->setEstado($municipio['estado']);
        }
        if (isset($municipio['codigo'])) {
            $this->setCodigo($municipio['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (isset($municipio['nome'])) {
            $this->setNome($municipio['nome']);
        } else {
            $this->setNome(null);
        }
        return $this;
    }
}
