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

/**
 * Classe que envia uma ou mais notas fiscais para os servidores da sefaz
 */
class SEFAZ {

	private $notas;
	private $configuracao;
	static private $instance;

	public function __construct($sefaz = array()) {
		$this->fromArray($sefaz);
	}

	public static function init() {
		if(is_null(self::$instance))
			self::$instance = new self();
		return self::getInstance();
	}

	public static function getInstance() {
		return self::$instance;
	}

	public function getNotas() {
		return $this->notas;
	}

	public function setNotas($notas) {
		$this->notas = $notas;
		return $this;
	}

	public function addNota($nota) {
		$this->notas[] = $nota;
		return $this;
	}

	public function getConfiguracao() {
		return $this->configuracao;
	}

	public function setConfiguracao($configuracao) {
		$this->configuracao = $configuracao;
		return $this;
	}

	public function toArray() {
		$sefaz = array();
		$sefaz['notas'] = $this->getNotas();
		$sefaz['configuracao'] = $this->getConfiguracao();
		return $sefaz;
	}

	public function fromArray($sefaz = array()) {
		if($sefaz instanceof SEFAZ)
			$sefaz = $sefaz->toArray();
		else if(!is_array($sefaz))
			return $this;
		$this->setNotas($sefaz['notas']);
		$this->setConfiguracao($sefaz['configuracao']);
		if(is_null($this->getConfiguracao()))
			$this->setConfiguracao(new Ajuste());
		return $this;
	}

	public function processa() {
		$evento = $this->getConfiguracao()->getEvento();
		foreach ($this->getNotas() as $nota) {
			try {
				$envia = true;
				do {
					$dom = $nota->getNode()->ownerDocument;
					if(!is_null($evento))
						$evento->onNotaGerada($nota, $dom);
					$dom = $nota->assinar($dom);
					$dom = $nota->validar($dom); // valida o XML da nota
					if(!is_null($evento))
						$evento->onNotaAssinada($nota, $dom);
					if(!$envia)
						break;
					if(!is_null($evento))
						$evento->onNotaEnviando($nota, $dom);
					$autorizacao = new NF\Autorizacao();
					try {
						$autorizacao->envia($nota, $dom);
					} catch (Exception $e) {
						if($nota->getEmissao() == NF::EMISSAO_CONTINGENCIA)
							throw $e;
						Log::debug('Mudando emissão para contingência: '.$e->getMessage());
						$envia = false;
						$nota->setEmissao(NF::EMISSAO_CONTINGENCIA);
						if(!is_null($evento))
							$evento->onFormaEmissao($nota, $nota->getEmissao());
						continue;
					}
					Log::debug('('.$autorizacao->getStatus().') - '.$autorizacao->getMotivo());
					if(is_null($nota->getProtocolo()))
						throw new Exception($autorizacao->getMotivo(), $autorizacao->getStatus());
					$dom = $nota->addProtocolo($dom);
					// $dom = $nota->validar($dom); // valida após protocolada
					Log::debug('('.$nota->getProtocolo()->getStatus().') - '.$nota->getProtocolo()->getMotivo());
					if(!is_null($evento))
						$evento->onNotaEnviada($nota, $dom);
					break;
				} while (true);
				if(!is_null($evento))
					$evento->onNotaCompleto($nota, $dom);
			} catch(Exception $e) {
				Log::error('('.$e->getCode().') - '.$e->getMessage());
				if(!is_null($evento))
					$evento->onNotaErro($nota, $e);
				else
					throw $e;
			}
		}
	}

}