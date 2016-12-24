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
namespace Imposto\COFINSST;
use \Imposto\COFINS\Aliquota as COFINSAliquota;

/**
 * Este grupo só deve ser informado se o produto for sujeito a COFINS por
 * ST, CST = 05, a informação deste grupo não desobriga a informação do
 * grupo COFINS.
 */
class Aliquota extends COFINSAliquota {

	public function __construct($aliquota = array()) {
		parent::__construct($aliquota);
		$this->setGrupo(self::GRUPO_COFINSST);
	}

	public function toArray() {
		$aliquota = parent::toArray();
		return $aliquota;
	}

	public function fromArray($aliquota = array()) {
		if($aliquota instanceof Aliquota)
			$aliquota = $aliquota->toArray();
		else if(!is_array($aliquota))
			return $this;
		parent::fromArray($aliquota);
		return $this;
	}

	public function getNode($name = null) {
		$element = parent::getNode(is_null($name)?'COFINSST':$name);
		$item = $element->getElementsByTagName('CST')->item(0);
		$element->removeChild($item);
		return $element;
	}

}