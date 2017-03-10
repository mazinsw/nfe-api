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

class Estado
{

    private $codigo;
    private $nome;
    private $uf;

    public function __construct($estado = array())
    {
        $this->fromArray($estado);
    }

    /**
     * Código do estado (utilizar a tabela do IBGE)
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
     * Nome do estado (Opcional)
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
     * Sigla do estado
     */
    public function getUF($normalize = false)
    {
        if (!$normalize) {
            return $this->uf;
        }
        return $this->uf;
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    public function checkCodigos()
    {
        if (is_numeric($this->getCodigo())) {
            return;
        }
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $this->setCodigo($db->getCodigoEstado($this->getUF()));
    }

    public function toArray($recursive = false)
    {
        $estado = array();
        $estado['codigo'] = $this->getCodigo();
        $estado['nome'] = $this->getNome();
        $estado['uf'] = $this->getUF();
        return $estado;
    }

    public function fromArray($estado = array())
    {
        if ($estado instanceof Estado) {
            $estado = $estado->toArray();
        } elseif (!is_array($estado)) {
            return $this;
        }
        if (isset($estado['codigo'])) {
            $this->setCodigo($estado['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (isset($estado['nome'])) {
            $this->setNome($estado['nome']);
        } else {
            $this->setNome(null);
        }
        if (isset($estado['uf'])) {
            $this->setUF($estado['uf']);
        } else {
            $this->setUF(null);
        }
        return $this;
    }
}
