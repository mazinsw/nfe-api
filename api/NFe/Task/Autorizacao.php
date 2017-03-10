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
namespace NFe\Task;

use NFe\Core\Nota;
use NFe\Core\SEFAZ;
use NFe\Common\Util;

class Autorizacao extends Retorno
{

    public function __construct($autorizacao = array())
    {
        parent::__construct($autorizacao);
    }

    public function toArray($recursive = false)
    {
        $autorizacao = parent::toArray($recursive);
        return $autorizacao;
    }

    public function fromArray($autorizacao = array())
    {
        if ($autorizacao instanceof Autorizacao) {
            $autorizacao = $autorizacao->toArray();
        } elseif (!is_array($autorizacao)) {
            return $this;
        }
        parent::fromArray($autorizacao);
        return $this;
    }

    private function getConteudo($dom)
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $dob = new \DOMDocument('1.0', 'UTF-8');
        $envio = $dob->createElement('enviNFe');
        $envio->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dob->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $envio->appendChild($versao);
        Util::appendNode($envio, 'idLote', self::genLote());
        Util::appendNode($envio, 'indSinc', $config->getSincrono(true));
        // Corrige xmlns:default
        // $data = $dob->importNode($dom->documentElement, true);
        // $envio->appendChild($data);
        Util::appendNode($envio, 'NFe', 0);
        $dob->appendChild($envio);
        // Corrige xmlns:default
        // return $dob;
        $xml = $dob->saveXML($dob->documentElement);
        return str_replace('<NFe>0</NFe>', $dom->saveXML($dom->documentElement), $xml);
    }

    public function envia($nota, $dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_AUTORIZACAO);
        $envio->setAmbiente($nota->getAmbiente());
        $envio->setModelo($nota->getModelo());
        $envio->setEmissao($nota->getEmissao());
        $envio->setConteudo($this->getConteudo($dom));
        $resp = $envio->envia();
        $this->loadNode($resp);
        if ($this->isProcessado()) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($resp);
            if ($protocolo->isAutorizado()) {
                $nota->setProtocolo($protocolo);
            }
            return $protocolo;
        } elseif ($this->isRecebido()) {
            $recibo = new Recibo($this->toArray());
            $recibo->setModelo($nota->getModelo());
            $recibo->loadNode($resp, Recibo::INFO_TAGNAME);
            return $recibo;
        }
        return $this;
    }

    public function loadNode($element, $name = null)
    {
        $tag = is_null($name)?'retEnviNFe':$name;
        $element = parent::loadNode($element, $tag);
        return $element;
    }
}
