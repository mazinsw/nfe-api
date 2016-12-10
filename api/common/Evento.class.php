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
 * Evento de emissão de nota fiscal eletrônica
 */
interface Evento {

	/**
	 * Chamado quando o XML da nota foi gerado
	 */
	public function onNotaGerada(&$nota, &$xml);

	/**
	 * Chamado após o XML da nota ser assinado
	 */
	public function onNotaAssinada(&$nota, &$xml);

	/**
	 * Chamado antes de enviar a nota para a SEFAZ
	 */
	public function onNotaEnviando(&$nota, &$xml);

	/**
	 * Chamado quando a forma de emissão da nota fiscal muda para normal ou
	 * contigência
	 */
	public function onFormaEmissao(&$nota, $forma);

	/**
	 * Chamado quando a nota foi enviada e aceita pela SEFAZ (Não é chamado
	 * quando em contigência)
	 */
	public function onNotaEnviada(&$nota, &$xml);

	/**
	 * Chamado quando a emissão da nota foi concluída com sucesso independente
	 * da forma de emissão
	 */
	public function onNotaCompleto(&$nota, &$xml);

	/**
	 * Chamado quando ocorre um erro nas etapas de geração e envio da nota (Não
	 * é chamado quando entra em contigência)
	 */
	public function onNotaErro(&$nota, $e);

}