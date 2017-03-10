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
namespace NFe\Entity\Imposto\ICMS;

use NFe\Entity\Imposto;

/**
 * Classe base do ICMS
 */
abstract class Base extends Imposto
{

    /**
     * origem da mercadoria: 0 - Nacional
     * 1 - Estrangeira - Importação direta
     *
     * 2 - Estrangeira - Adquirida no mercado interno
     */
    const ORIGEM_NACIONAL = 'nacional';
    const ORIGEM_ESTRANGEIRA = 'estrangeira';
    const ORIGEM_INTERNO = 'interno';

    private $origem;

    public function __construct($base = array())
    {
        parent::__construct($base);
        $this->setGrupo(self::GRUPO_ICMS);
    }

    /**
     * origem da mercadoria: 0 - Nacional
     * 1 - Estrangeira - Importação direta
     *
     * 2 - Estrangeira - Adquirida no mercado interno
     */
    public function getOrigem($normalize = false)
    {
        if (!$normalize) {
            return $this->origem;
        }
        switch ($this->origem) {
            case self::ORIGEM_NACIONAL:
                return '0';
            case self::ORIGEM_ESTRANGEIRA:
                return '1';
            case self::ORIGEM_INTERNO:
                return '2';
        }
        return $this->origem;
    }

    public function setOrigem($origem)
    {
        $this->origem = $origem;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $base = parent::toArray($recursive);
        $base['origem'] = $this->getOrigem();
        return $base;
    }

    public function fromArray($base = array())
    {
        if ($base instanceof Base) {
            $base = $base->toArray();
        } elseif (!is_array($base)) {
            return $this;
        }
        parent::fromArray($base);
        if (!isset($base['origem']) || is_null($base['origem'])) {
            $this->setOrigem(self::ORIGEM_NACIONAL);
        } else {
            $this->setOrigem($base['origem']);
        }
        return $this;
    }
}
