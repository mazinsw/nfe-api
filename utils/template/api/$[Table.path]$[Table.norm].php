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
$[table.if(package)]
namespace $[table.package];
$[table.end]

use NFe\Common\Util;
$[table.if(inherited)]$[table.else]
use NFe\Common\Node;
$[table.end]

$[table.if(comment)]
/**
$[table.each(comment)]
 * $[Table.comment]
$[table.end]
 */
$[table.end]
class $[Table.norm]$[table.if(inherited)] extends $[table.inherited]$[table.else] implements Node$[table.end]

{
$[field.each(all)]
$[field.if(enum)]
$[field.if(comment)]
    /**
$[field.each(comment)]
     * $[Field.comment]
$[field.end]
     */
$[field.end]
$[field.each(option)]
    public const $[FIELD.unix]_$[FIELD.option.norm] = '$[field.option]';
$[field.end]

$[field.end]
$[field.end]
$[field.each(all)]
$[field.if(comment)]
    /**
$[field.each(comment)]
     * $[Field.comment]
$[field.end]
     *
     * @var $[field.if(integer|bigint)]int$[field.else.if(float|double|currency)]float$[field.else]string$[field.end]$[field.if(array)][]$[field.end]

     */
$[field.end]
    private $$[field.unix];

$[field.end]
    /**
     * Constroi uma instância de $[Table.norm] vazia
     * @param array $$[table.unix] Array contendo dados d$[table.gender] $[Table.norm]
     */
    public function __construct($$[table.unix] = [])
    {
$[table.if(inherited)]
        parent::__construct($$[table.unix]);
$[table.else]
        $this->fromArray($$[table.unix]);
$[table.end]
    }
$[field.each(all)]

$[field.if(comment)]
    /**
$[field.each(comment)]
     * $[Field.comment]
$[field.end]
$[field.if(searchable)]$[field.else]
     * @param boolean $normalize informa se $[field.gender] $[fIeld.name] deve estar no formato do XML
$[field.end]
$[field.if(array)]
     * @param int $number number to get $[Field.norm]
$[field.end]
$[field.if(integer|bigint)]
     * @return int $[field.name] of $[Table.name]
$[field.else.if(float|double|currency)]
     * @return float $[field.name] of $[Table.name]
$[field.else]
     * @return string $[field.name] of $[Table.name]
$[field.end]
     */
$[field.end]
    public function get$[Field.norm]($[field.if(searchable)]$[field.else]$normalize = false$[field.end])
    {
$[field.if(searchable)]
        return $this->$[field.unix];
$[field.else]
        if (!$normalize$[field.if(datetime)] || is_null($this->$[field.unix])$[field.end]) {
            return $this->$[field.unix];
        }
$[field.if(currency)]
        return Util::toCurrency($this->$[field.unix]);
$[field.else.if(float|double)]
        return Util::toFloat($this->$[field.unix]);
$[field.else.if(datetime)]
        return Util::toDateTime($this->$[field.unix]);
$[field.else.if(enum)]
        switch ($this->$[field.unix]) {
$[field.each(option)]
            case self::$[FIELD.unix]_$[FIELD.option.norm]:
                return '$[fIeld.option.name]';
$[field.end]
        }
        return $this->$[field.unix];
$[field.else.if(boolean)]
        return $this->is$[Field.norm]() ? '1' : '0';
$[field.else]
        return $this->$[field.unix];
$[field.end]
$[field.end]
    }
$[field.if(boolean)]

$[field.if(comment)]
    /**
$[field.each(comment)]
     * $[Field.comment]
$[field.end]
     * @return boolean informa se $[field.gender] $[Field.norm] está habilitado
     */
$[field.end]
    public function is$[Field.norm]()
    {
        return $this->$[field.unix] == 'Y';
    }
$[field.end]
    
    /**
     * Altera o valor d$[field.gender] $[Field.norm] para o informado no parâmetro
     * @param mixed $$[field.unix] novo valor para $[Field.norm]
$[field.if(integer|bigint)]
     * @param int $$[field.unix] Novo $[field.name] para $[Table.name]
$[field.else.if(float|double|currency)]
     * @param float $$[field.unix] Novo $[field.name] para $[Table.name]
$[field.else]
     * @param string $$[field.unix] Novo $[field.name] para $[Table.name]
$[field.end]
     * @return self A própria instância da classe
     */
    public function set$[Field.norm]($$[field.unix])
    {
$[field.if(enum)]
        switch ($$[field.unix]) {
$[field.each(option)]
            case '$[fIeld.option.name]':
                $$[field.unix] = self::$[FIELD.unix]_$[FIELD.option.norm];
                break;
$[field.end]
        }
$[field.else.if(integer)]
        if (trim($$[field.unix]) != '') {
            $$[field.unix] = intval($$[field.unix]);
        }
$[field.else.if(currency)]
        if (trim($$[field.unix]) != '') {
            $$[field.unix] = floatval($$[field.unix]);
        }
$[field.else.if(float|double)]
        if (trim($$[field.unix]) != '') {
            $$[field.unix] = floatval($$[field.unix]);
        }
$[field.else.if(datetime)]
        if ($[field.if(null)]trim($$[field.unix]) != '' && $[field.end]!is_numeric($$[field.unix])) {
            $$[field.unix] = strtotime($$[field.unix]);
        }
$[field.else.if(boolean)]
        if ($[field.if(null)]trim($$[field.unix]) != '' && $[field.end]is_bool($$[field.unix])) {
            $$[field.unix] = $$[field.unix] ? 'Y': 'N';
        }
$[field.end]
        $this->$[field.unix] = $$[field.unix];
        return $this;
    }
$[field.if(descriptor)]
$[field.else.if(searchable)]

    /**
     * Adiciona um(a) $[Field.unix.plural] para a lista de $[fIeld.unix.plural]
     * @param $[Field.unix.plural] $$[field.unix.plural] Instância d$[field.gender] $[Field.unix.plural] que será adicionada
     * @return self A própria instância da classe
     */
    public function add$[Field.unix.plural]($$[field.unix.plural])
    {
        $this->$[field.unix][] = $$[field.unix.plural];
        return $this;
    }
$[field.end]
$[field.end]

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
$[table.if(inherited)]
        $$[table.unix] = parent::toArray($recursive);
$[table.else]
        $$[table.unix] = [];
$[table.end]
$[field.each(all)]
$[field.if(descriptor)]
        if (!is_null($this->get$[Field.norm]()) && $recursive) {
            $$[table.unix]['$[field]'] = $this->get$[Field.norm]()->toArray($recursive);
        } else {
            $$[table.unix]['$[field]'] = $this->get$[Field.norm]();
        }
$[field.else.if(searchable)]
        if ($recursive) {
            $$[field.unix] = [];
            $_$[field.unix] = $this->get$[Field.norm]();
            foreach ($_$[field.unix] as $_$[field.unix.plural]) {
                $$[field.unix][] = $_$[field.unix.plural]->toArray($recursive);
            }
            $$[table.unix]['$[field]'] = $$[field.unix];
        } else {
            $$[table.unix]['$[field]'] = $this->get$[Field.norm]();
        }
$[field.else.if(datetime)]
        $$[table.unix]['$[field]'] = $this->get$[Field.norm]($recursive);
$[field.else]
        $$[table.unix]['$[field]'] = $this->get$[Field.norm]();
$[field.end]
$[field.end]
        return $$[table.unix];
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $$[table.unix] Array ou instância de $[Table.norm], para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($$[table.unix] = [])
    {
        if ($$[table.unix] instanceof $[Table.norm]) {
            $$[table.unix] = $$[table.unix]->toArray();
        } elseif (!is_array($$[table.unix])) {
            return $this;
        }
$[table.if(inherited)]
        parent::fromArray($$[table.unix]);
$[table.end]
$[field.each(all)]
$[field.if(null)]
        if (!array_key_exists('$[field]', $$[table.unix])) {
$[field.else]
        if (!isset($$[table.unix]['$[field]'])) {
$[field.end]
$[field.if(default)]
            $this->set$[Field.norm]($[fIeld.info]);
$[field.else]
            $this->set$[Field.norm](null);
$[field.end]
        } else {
            $this->set$[Field.norm]($$[table.unix]['$[field]']);
        }
$[field.end]
        return $this;
    }

    /**
     * Cria um nó XML d$[table.gender] $[table.norm] de acordo com o leiaute da NFe
     * @param string $name Nome do nó que será criado
     * @return DOMElement Nó que contém todos os campos da classe
     */
    public function getNode($name = null)
    {
$[table.if(inherited)]
        $element = parent::getNode(is_null($name) ? '$[tAble.style]' : $name);
        $dom = $element->ownerDocument;
$[table.else]
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? '$[tAble.style]' : $name);
$[table.end]
$[field.each(all)]
$[field.if(null)]
        if (!is_null($this->get$[Field.norm]())) {
$[field.end]
$[field.if(descriptor)]
    $[field.if(null)]    $[field.end]    $$[field.unix] = $this->get$[Field.norm]()->getNode();
    $[field.if(null)]    $[field.end]    $$[field.unix] = $dom->importNode($$[field.unix], true);
    $[field.if(null)]    $[field.end]    $element->appendChild($$[field.unix]);
$[field.else.if(searchable)]
    $[field.if(null)]    $[field.end]    $_$[field.unix] = $this->get$[Field.norm]();
    $[field.if(null)]    $[field.end]    $$[field.unix] = $dom->createElement('$[fIeld.style]');
    $[field.if(null)]    $[field.end]    foreach ($_$[field.unix] as $_$[field.unix.plural]) {
    $[field.if(null)]    $[field.end]        $$[field.unix.plural] = $_$[field.unix.plural]->getNode();
    $[field.if(null)]    $[field.end]        $$[field.unix.plural] = $dom->importNode($$[field.unix.plural], true);
    $[field.if(null)]    $[field.end]        $$[field.unix]->appendChild($$[field.unix.plural]);
    $[field.if(null)]    $[field.end]    }
    $[field.if(null)]    $[field.end]    $element->appendChild($$[field.unix]);
$[field.else]
    $[field.if(null)]    $[field.end]    Util::appendNode($element, '$[fIeld.style]', $this->get$[Field.norm](true));
$[field.end]
$[field.if(null)]
        }
$[field.end]
$[field.end]
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param DOMElement $element Nó do xml com todos as tags dos campos
     * @param string $name Nome do nó que será carregado
     * @return DOMElement Instância do nó que foi carregado
     */
    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? '$[tAble.style]' : $name;
$[table.if(inherited)]
        $element = parent::loadNode($element, $name);
$[table.else]
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception("Tag \"$name\" d$[table.gender] $[Table.norm] não encontrada", 404);
            }
            $element = $_fields->item(0);
        }
$[table.end]
$[field.each(all)]
$[field.if(descriptor)]
        $_fields = $element->getElementsByTagName('$[fIeld.style]');
$[field.if(null)]
        $$[field.unix] = null;
$[field.end]
        if ($_fields->length > 0) {
$[field.if(default)]
            $$[field.unix] = $[fIeld.info];
$[field.else]
            $$[field.unix] = new $[Field.norm](); // TODO: predictable class name
$[field.end]
            $$[field.unix]->loadNode($_fields->item(0), '$[fIeld.style]');
$[field.if(null)]
        }
$[field.else]
        } else {
            throw new \Exception('Tag "$[fIeld.style]" do objeto "$[Field.norm]" não encontrada n$[table.gender] $[Table.norm]', 404);
        }
$[field.end]
        $this->set$[Field.norm]($$[field.unix]);
$[field.else.if(searchable)]
        $$[field.unix] = [];
        $_fields = $element->getElementsByTagName('$[fIeld.norm.plural]'); // TODO: predictable tag name
        if ($_fields->length > 0) {
            $_items = $_fields->item(0)->getElementsByTagName('$[fIeld.style]');
            foreach ($_items as $_item) {
$[field.if(default)]
                $$[field.unix.plural] = $[fIeld.info];
$[field.else]
                $$[field.unix.plural] = new $[Field.norm](); // TODO: predictable class name
$[field.end]
                $$[field.unix.plural]->loadNode($_item, '$[fIeld.style]');
                $$[field.unix][] = $$[field.unix.plural];
            }
$[field.if(null)]
        }
$[field.else]
        } else {
            throw new \Exception('Tag "$[fIeld.norm.plural]" da lista de "$[Field.norm]" não encontrada n$[table.gender] $[Table.norm]', 404); // TODO: predictable tag name
        }
$[field.end]
        $this->set$[Field.norm]($$[field.unix]);
$[field.else]
$[field.if(null)]
        $this->set$[Field.norm](Util::loadNode($element, '$[fIeld.style]'));
$[field.else]
        $this->set$[Field.norm](
            Util::loadNode(
                $element,
                '$[fIeld.style]',
                'Tag "$[fIeld.style]" não encontrada n$[table.gender] $[Table.norm]'
            )
        );
$[field.end]
$[field.end]
$[field.end]
        return $element;
    }
}
