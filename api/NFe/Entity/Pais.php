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

class Pais
{

    private $codigo;
    private $nome;

    public function __construct($pais = array())
    {
        $this->fromArray($pais);
    }

    /**
     * Código do país
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
     * Nome do país
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

    public function toArray($recursive = false)
    {
        $pais = array();
        $pais['codigo'] = $this->getCodigo();
        $pais['nome'] = $this->getNome();
        return $pais;
    }

    public function fromArray($pais = array())
    {
        if ($pais instanceof Pais) {
            $pais = $pais->toArray();
        } elseif (!is_array($pais)) {
            return $this;
        }
        if (isset($pais['codigo'])) {
            $this->setCodigo($pais['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (isset($pais['nome'])) {
            $this->setNome($pais['nome']);
        } else {
            $this->setNome(null);
        }
        return $this;
    }
}
