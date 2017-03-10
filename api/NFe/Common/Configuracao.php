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

use NFe\Entity\Emitente;
use NFe\Database\Estatico;

/**
 * Fornece informações importante para a geração e envio das notas fiscais
 */
class Configuracao
{

    private $banco;
    private $emitente;
    private $evento;
    private $chave_publica;
    private $chave_privada;
    private $arquivo_chave_publica;
    private $arquivo_chave_privada;
    private $token;
    private $csc;
    private $token_ibpt;
    private $tempo_limite;
    private $sincrono;
    private $offline;

    public function __construct($configuracao = array())
    {
        $this->fromArray($configuracao);
    }

    /**
     * Banco que fornece informações sobre items da nota como: Códigos e Taxas
     */
    public function getBanco()
    {
        return $this->banco;
    }

    public function setBanco($banco)
    {
        $this->banco = $banco;
        return $this;
    }

    /**
     * Emitente da nota fiscal
     */
    public function getEmitente()
    {
        return $this->emitente;
    }

    public function setEmitente($emitente)
    {
        $this->emitente = $emitente;
        return $this;
    }

    /**
     * Informa a instancia que receberá os eventos do processamento das notas
     */
    public function getEvento()
    {
        return $this->evento;
    }

    public function setEvento($evento)
    {
        $this->evento = $evento;
        return $this;
    }

    /**
     * Conteúdo da chave pública ou certificado no formato PEM
     */
    public function getChavePublica()
    {
        return $this->chave_publica;
    }

    public function setChavePublica($chave_publica)
    {
        $this->chave_publica = $chave_publica;
        return $this;
    }

    /**
     * Conteúdo da chave privada do certificado no formato PEM
     */
    public function getChavePrivada()
    {
        return $this->chave_privada;
    }

    public function setChavePrivada($chave_privada)
    {
        $this->chave_privada = $chave_privada;
        return $this;
    }

    /**
     * Informa o caminho do arquivo da chave pública ou certificado no formato
     * PEM
     */
    public function getArquivoChavePublica()
    {
        return $this->arquivo_chave_publica;
    }

    public function setArquivoChavePublica($arquivo_chave_publica)
    {
        $this->arquivo_chave_publica = $arquivo_chave_publica;
        if (file_exists($arquivo_chave_publica)) {
            $this->setChavePublica(file_get_contents($arquivo_chave_publica));
        }
        return $this;
    }

    /**
     * Caminho do arquivo da chave privada do certificado no formato PEM
     */
    public function getArquivoChavePrivada()
    {
        return $this->arquivo_chave_privada;
    }

    public function setArquivoChavePrivada($arquivo_chave_privada)
    {
        $this->arquivo_chave_privada = $arquivo_chave_privada;
        if (file_exists($arquivo_chave_privada)) {
            $this->setChavePrivada(file_get_contents($arquivo_chave_privada));
        }
        return $this;
    }

    /**
     * Token do CSC
     */
    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Código do contribuinte para emissão de nota fiscal
     */
    public function getCSC()
    {
        return $this->csc;
    }

    public function setCSC($csc)
    {
        $this->csc = $csc;
        return $this;
    }

    /**
     * Token IBPT para consulta de impostos online
     */
    public function getTokenIBPT()
    {
        return $this->token_ibpt;
    }

    public function setTokenIBPT($token_ibpt)
    {
        $this->token_ibpt = $token_ibpt;
        return $this;
    }

    /**
     * Tempo limite em segundos nas conexões com os Web services, 0 para sem tempo limite
     */
    public function getTempoLimite()
    {
        return $this->tempo_limite;
    }

    public function setTempoLimite($tempo_limite)
    {
        $this->tempo_limite = intval($tempo_limite);
        return $this;
    }

    /**
     * Informa se o processo de autorização da nota é síncrono ou assíncrono
     */
    public function getSincrono($normalize = false)
    {
        if (!$normalize) {
            return $this->sincrono;
        }
        return $this->isSincrono()?'1':'0';
    }

    /**
     * Informa se o processo de autorização da nota é síncrono ou assíncrono
     */
    public function isSincrono()
    {
        return $this->sincrono == 'Y';
    }

    public function setSincrono($sincrono)
    {
        if (!in_array($sincrono, array('N', 'Y'))) {
            $sincrono = $sincrono?'Y':'N';
        }
        $this->sincrono = $sincrono;
        return $this;
    }

    /**
     * Informa se está operando offline
     * @return mixed offline da Configuracao
     */
    public function getOffline($normalize = false)
    {
        if (!$normalize || is_null($this->offline)) {
            return $this->offline;
        }
        return Util::toDateTime($this->getOffline());
    }

    /**
     * Informa se está operando offline
     */
    public function isOffline()
    {
        return $this->offline + 180 > time();
    }

    /**
     * Entra no modo offline e sai automaticamente após 3 minutos
     */
    public function setOffline($offline)
    {
        if (!is_null($offline) && !is_numeric($offline)) {
            $offline = strtotime($offline);
        }
        $this->offline = $offline;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $configuracao = array();
        $configuracao['banco'] = $this->getBanco();
        $configuracao['emitente'] = $this->getEmitente();
        $configuracao['evento'] = $this->getEvento();
        $configuracao['arquivo_chave_publica'] = $this->getArquivoChavePublica();
        $configuracao['arquivo_chave_privada'] = $this->getArquivoChavePrivada();
        $configuracao['token'] = $this->getToken();
        $configuracao['csc'] = $this->getCSC();
        $configuracao['token_ibpt'] = $this->getTokenIBPT();
        $configuracao['tempo_limite'] = $this->getTempoLimite();
        $configuracao['sincrono'] = $this->getSincrono();
        $configuracao['offline'] = $this->getOffline($recursive);
        return $configuracao;
    }

    public function fromArray($configuracao = array())
    {
        if ($configuracao instanceof Configuracao) {
            $configuracao = $configuracao->toArray();
        } elseif (!is_array($configuracao)) {
            return $this;
        }
        if (!isset($configuracao['banco']) || is_null($configuracao['banco'])) {
            $this->setBanco(new Estatico());
        } else {
            $this->setBanco($configuracao['banco']);
        }
        if (!isset($configuracao['emitente']) || is_null($configuracao['emitente'])) {
            $this->setEmitente(new Emitente());
        } else {
            $this->setEmitente($configuracao['emitente']);
        }
        if (isset($configuracao['evento'])) {
            $this->setEvento($configuracao['evento']);
        } else {
            $this->setEvento(null);
        }
        if (isset($configuracao['chave_publica'])) {
            $this->setChavePublica($configuracao['chave_publica']);
        } else {
            $this->setChavePublica(null);
        }
        if (isset($configuracao['chave_privada'])) {
            $this->setChavePrivada($configuracao['chave_privada']);
        } else {
            $this->setChavePrivada(null);
        }
        if (isset($configuracao['arquivo_chave_publica'])) {
            $this->setArquivoChavePublica($configuracao['arquivo_chave_publica']);
        } else {
            $this->setArquivoChavePublica(null);
        }
        if (isset($configuracao['arquivo_chave_privada'])) {
            $this->setArquivoChavePrivada($configuracao['arquivo_chave_privada']);
        } else {
            $this->setArquivoChavePrivada(null);
        }
        if (isset($configuracao['token'])) {
            $this->setToken($configuracao['token']);
        } else {
            $this->setToken(null);
        }
        if (isset($configuracao['csc'])) {
            $this->setCSC($configuracao['csc']);
        } else {
            $this->setCSC(null);
        }
        if (isset($configuracao['token_ibpt'])) {
            $this->setTokenIBPT($configuracao['token_ibpt']);
        } else {
            $this->setTokenIBPT(null);
        }
        if (!isset($configuracao['tempo_limite']) || is_null($configuracao['tempo_limite'])) {
            $this->setTempoLimite(4);
        } else {
            $this->setTempoLimite($configuracao['tempo_limite']);
        }
        if (!isset($configuracao['sincrono']) || is_null($configuracao['sincrono'])) {
            $this->setSincrono('Y');
        } else {
            $this->setSincrono($configuracao['sincrono']);
        }
        return $this;
    }
}
