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
namespace NFe\Common;

/**
 * Evento de emissão de nota fiscal eletrônica
 */
interface Evento
{
    /**
     * Chamado quando o XML da nota foi gerado
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaGerada($nota, $xml);

    /**
     * Chamado após o XML da nota ser assinado
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaAssinada($nota, $xml);

    /**
     * Chamado após o XML da nota ser validado com sucesso
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaValidada($nota, $xml);

    /**
     * Chamado antes de enviar a nota para a SEFAZ
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaEnviando($nota, $xml);

    /**
     * Chamado quando a forma de emissão da nota fiscal muda para contigência,
     * aqui deve ser decidido se o número da nota deverá ser pulado e se esse
     * número deve ser cancelado ou inutilizado
     * @param \NFe\Core\Nota $nota
     * @param bool $offline
     * @param \Exception $exception
     */
    public function onNotaContingencia($nota, $offline, $exception);

    /**
     * Chamado quando a nota foi enviada e aceita pela SEFAZ
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaAutorizada($nota, $xml, $retorno);

    /**
     * Chamado quando a emissão da nota foi concluída com sucesso independente
     * da forma de emissão
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaCompleto($nota, $xml);

    /**
     * Chamado quando uma nota é rejeitada pela SEFAZ, a nota deve ser
     * corrigida para depois ser enviada novamente
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaRejeitada($nota, $xml, $retorno);

    /**
     * Chamado quando a nota é denegada e não pode ser utilizada (outra nota
     * deve ser gerada)
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaDenegada($nota, $xml, $retorno);

    /**
     * Chamado após tentar enviar uma nota e não ter certeza se ela foi
     * recebida ou não (problemas técnicos), deverá ser feito uma consulta pela
     * chave para obter o estado da nota
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \Exception $exception
     */
    public function onNotaPendente($nota, $xml, $exception);

    /**
     * Chamado quando uma nota é enviada, mas não retornou o protocolo que será
     * consultado mais tarde
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaProcessando($nota, $xml, $retorno);

    /**
     * Chamado quando uma nota autorizada é cancelada na SEFAZ
     * @param \NFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \NFe\Task\Retorno $retorno
     */
    public function onNotaCancelada($nota, $xml, $retorno);

    /**
     * Chamado quando ocorre um erro nas etapas de geração e envio da nota
     * @param \NFe\Core\Nota $nota
     * @param \Exception $exception
     */
    public function onNotaErro($nota, $exception);

    /**
     * Chamado quando um ou mais números de notas forem inutilizados
     * @param \NFe\Task\Inutilizacao $inutilizacao
     * @param \DOMDocument $xml
     */
    public function onInutilizado($inutilizacao, $xml);

    /**
     * Chamado quando uma tarefa é executada
     * @param \NFe\Task\Tarefa $tarefa
     * @param \NFe\Task\Retorno $retorno
     */
    public function onTarefaExecutada($tarefa, $retorno);

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     * @param \NFe\Task\Tarefa $tarefa
     * @param \Exception $exception
     */
    public function onTarefaErro($tarefa, $exception);
}
