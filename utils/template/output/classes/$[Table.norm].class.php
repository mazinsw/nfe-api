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
$[field.each(all)]
$[field.if(enum)]

class $[Table.norm]$[Field.norm] {
$[field.each(option)]
	const $[FIELD.option.norm] = '$[field.option]';
$[field.end]
}
$[field.end]
$[field.end]

$[table.if(comment)]
/**
$[table.each(comment)]
 * $[Table.comment]
$[table.end]
 */
$[table.end]
class $[Table.norm] implements NodeInterface {

$[field.each(all)]
	private $$[field.unix];
$[field.end]

	public function __construct($$[table.unix] = array()) {
		$this->fromArray($$[table.unix]);
	}
$[field.each(all)]

$[field.if(comment)]
	/**
$[field.each(comment)]
	 * $[Field.comment]
$[field.end]
	 */
$[field.end]
	public function get$[Field.norm]($[field.if(searchable)]$[field.else]$normalize = false$[field.end]) {
$[field.if(searchable)]
		return $this->$[field.unix];
$[field.else]
		if(!$normalize)
			return $this->$[field.unix];
$[field.if(currency)]
		return Util::toCurrency($this->$[field.unix]);
$[field.else.if(float|double)]
		return Util::toFloat($this->$[field.unix]);
$[field.else.if(datetime)]
		return Util::toDateTime($this->$[field.unix]);
$[field.else.if(enum)]
		switch ($this->$[field.unix]) {
$[field.each(option)]
			case $[Table.norm]$[Field.norm]::$[FIELD.option.norm]:
				return '$[fIeld.option.name]';
$[field.end]
		}
		return $this->$[field.unix];
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
	 */
$[field.end]
	public function is$[Field.norm]() {
		return $this->$[field.unix] == 'Y';
	}
$[field.end]

	public function set$[Field.norm]($$[field.unix]) {
		$this->$[field.unix] = $$[field.unix];
		return $this;
	}
$[field.if(descriptor)]
$[field.else.if(searchable)]

	public function add$[Field.unix.plural]($$[field.unix.plural]) {
		$this->$[field.unix][] = $$[field.unix.plural];
		return $this;
	}
$[field.end]
$[field.end]

	public function toArray() {
		$$[table.unix] = array();
$[field.each(all)]
		$$[table.unix]['$[field]'] = $this->get$[Field.norm]();
$[field.end]
		return $$[table.unix];
	}

	public function fromArray($$[table.unix] = array()) {
		if($$[table.unix] instanceof $[Table.norm])
			$$[table.unix] = $$[table.unix]->toArray();
		else if(!is_array($$[table.unix]))
			return $this;
$[field.each(all)]
		$this->set$[Field.norm]($$[table.unix]['$[field]']);
$[field.if(default)]
		if(is_null($this->get$[Field.norm]()))
			$this->set$[Field.norm]($[fIeld.info]);
$[field.end]
$[field.end]
		return $this;
	}

	public function getNode($name = null) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->createElement(is_null($name)?'$[tAble.style]':$name);
$[field.each(all)]
$[field.if(null)]
		if(!is_null($this->get$[Field.norm]()))
			$element->appendChild($dom->createElement('$[fIeld.style]', $this->get$[Field.norm](true)));
$[field.else.if(descriptor)]
		$$[field.unix] = $this->get$[Field.norm]()->getNode();
		$$[field.unix] = $dom->importNode($$[field.unix], true);
		$element->appendChild($$[field.unix]);
$[field.else.if(searchable)]
		$_$[field.unix] = $this->get$[Field.norm]();
		$$[field.unix] = $dom->createElement('$[fIeld.style]');
		foreach ($_$[field.unix] as $_$[field.unix.plural]) {
			$$[field.unix.plural] = $_$[field.unix.plural]->getNode();
			$$[field.unix.plural] = $dom->importNode($$[field.unix.plural], true);
			$$[field.unix]->appendChild($$[field.unix.plural]);
		}
		$element->appendChild($$[field.unix]);
$[field.else]
		$element->appendChild($dom->createElement('$[fIeld.style]', $this->get$[Field.norm](true)));
$[field.end]
$[field.end]
		return $element;
	}

}