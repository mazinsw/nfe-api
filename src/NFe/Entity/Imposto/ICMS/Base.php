<?php
/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
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
use NFe\Common\Util;
use NFe\Entity\Imposto\Fundo\Base as Fundo;
use NFe\Entity\Imposto\Fundo\Retido;
use NFe\Entity\Imposto\Fundo\Substituido;

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
    public const ORIGEM_NACIONAL = 'nacional';
    public const ORIGEM_ESTRANGEIRA = 'estrangeira';
    public const ORIGEM_INTERNO = 'interno';

    /**
     * origem da mercadoria:
     * 0 - Nacional
     * 1 - Estrangeira - Importação direta
     * 2
     * - Estrangeira - Adquirida no mercado interno
     */
    private $origem;

    /**
     * Fundo de Combate à Probreza
     */
    private $fundo;

    /**
     * Constroi uma instância de Base vazia
     * @param  array $base Array contendo dados do Base
     */
    public function __construct($base = [])
    {
        parent::__construct($base);
        $this->setGrupo(self::GRUPO_ICMS);
    }

    /**
     * origem da mercadoria:
     * 0 - Nacional
     * 1 - Estrangeira - Importação direta
     * 2
     * - Estrangeira - Adquirida no mercado interno
     * @param boolean $normalize informa se a origem deve estar no formato do XML
     * @return mixed origem do Base
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

    /**
     * Altera o valor da Origem para o informado no parâmetro
     * @param mixed $origem novo valor para Origem
     * @return Base A própria instância da classe
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;
        return $this;
    }

    /**
     * Fundo de Combate à Probreza
     * @return \NFe\Entity\Imposto\Fundo\Base Base do fundo de combate à pobreza
     */
    public function getFundo()
    {
        return $this->fundo;
    }
    
    /**
     * Altera o valor do Fundo para o informado no parâmetro
     * @param mixed $fundo novo valor para Fundo
     * @return Base A própria instância da classe
     */
    public function setFundo($fundo)
    {
        $this->fundo = $fundo;
        return $this;
    }

    protected function exportFundo($element)
    {
        if (is_null($this->getFundo()) || is_null($this->getFundo()->getAliquota())) {
            return $element;
        }
        $fundo = $this->getFundo()->getNode();
        return Util::mergeNodes($element, $fundo);
    }

    protected function importFundo($element)
    {
        if (is_null($this->getFundo())) {
            return $this;
        }
        if (!$this->getFundo()->exists($element)) {
            $this->getFundo()->fromArray([]);
        } else {
            $this->getFundo()->loadNode($element, $element->nodeName);
        }
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $base = parent::toArray($recursive);
        $base['origem'] = $this->getOrigem();
        if (!is_null($this->getFundo()) && $recursive) {
            $base['fundo'] = $this->getFundo()->toArray($recursive);
        } else {
            $base['fundo'] = $this->getFundo();
        }
        return $base;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $base Array ou instância de Base, para copiar os valores
     * @return Base A própria instância da classe
     */
    public function fromArray($base = [])
    {
        if ($base instanceof Base) {
            $base = $base->toArray();
        } elseif (!is_array($base)) {
            return $this;
        }
        parent::fromArray($base);
        if (!isset($base['origem'])) {
            $this->setOrigem(self::ORIGEM_NACIONAL);
        } else {
            $this->setOrigem($base['origem']);
        }
        if (isset($base['fundo']) && $base['fundo'] instanceof Fundo) {
            $this->setFundo(clone $base['fundo']);
        } elseif (isset($base['fundo']['grupo'])) {
            switch ($base['fundo']['grupo']) {
                case Fundo::GRUPO_FCPST:
                    $this->setFundo(new Substituido($base['fundo']));
                    break;
                case Fundo::GRUPO_FCPSTRET:
                    $this->setFundo(new Retido($base['fundo']));
                    break;
                default: //Fundo::GRUPO_FCP
                    $this->setFundo(new Fundo($base['fundo']));
                    break;
            }
        } else {
            $this->setFundo(null);
        }
        return $this;
    }
}
