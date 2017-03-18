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

use NFe\Common\Util;

/**
 * Grupo de informação do ICMSST devido para a UF de destino, nas operações
 * interestaduais de produtos que tiveram retenção antecipada de ICMS por
 * ST na UF do remetente. Repasse via Substituto Tributário.
 */
class Substituto extends Cobrado
{

    public function __construct($substituto = array())
    {
        parent::__construct($substituto);
        $this->setTributacao('41');
        $this->setNormal(new Cobrado());
    }

    public function toArray($recursive = false)
    {
        $substituto = parent::toArray($recursive);
        return $substituto;
    }

    public function fromArray($substituto = array())
    {
        if ($substituto instanceof Substituto) {
            $substituto = $substituto->toArray();
        } elseif (!is_array($substituto)) {
            return $this;
        }
        parent::fromArray($substituto);
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name)?'ICMSST':$name);
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'vBCSTDest', $this->getNormal()->getBase(true));
        Util::appendNode($element, 'vICMSSTDest', $this->getNormal()->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'ICMSST':$name;
        $element = parent::loadNode($element, $name);
        $this->getNormal()->setBase(
            Util::loadNode(
                $element,
                'vBCSTDest',
                'Tag "vBCSTDest" do campo "Normal.Base" não encontrada no Substituto'
            )
        );
        $this->getNormal()->setValor(
            Util::loadNode(
                $element,
                'vICMSSTDest',
                'Tag "vICMSSTDest" do campo "Normal.Valor" não encontrada no Substituto'
            )
        );
        return $element;
    }
}
