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
namespace NFe\Database;

use Curl\Curl;
use NFe\Log\Logger;

class IBPT
{

    private $tabela;
    private $offline;

    public function __construct()
    {
        $this->tabela = array();
        $this->offline = false;
    }

    public function isOffline()
    {
        return $this->offline;
    }

    public function setOffline($offline)
    {
        $this->offline = $offline;
    }

    private function load($uf)
    {
        if (isset($this->tabela[$uf])) {
            return $this->tabela[$uf];
        }
        $file = __DIR__ . '/data/IBPT/'.$uf.'.json';
        if (!file_exists($file)) {
            return false;
        }
        $content = file_get_contents($file);
        if ($content === false) {
            return false;
        }
        $data = json_decode($content, true);
        $this->tabela[$uf] = $data;
        return $data;
    }

    private function getImpostoOffline($ncm, $uf, $ex)
    {
        $data = $this->load($uf);
        if ($data === false) {
            return false;
        }
        $key = $ncm.'.'.sprintf('%02s', $ex);
        $o = $data['estados'][$uf][$key];
        if (is_null($o)) {
            return false;
        }
        $o['info'] = $data['info'];
        $o['info']['origem'] = 'Tabela offline';
        return $o;
    }

    private function getImpostoOnline($cnpj, $token, $ncm, $uf, $ex)
    {
        if ($this->isOffline()) {
            return false;
        }
        $url = 'http://iws.ibpt.org.br/api/Produtos';
        $params = array(
            'token' => $token,
            'cnpj' => $cnpj,
            'codigo' => $ncm,
            'uf' => $uf,
            'ex' => intval($ex)
        );
        $curl = new Curl($url);
        $curl->setConnectTimeout(2);
        $curl->setTimeout(3);
        $data = $curl->get($params);
        if ($curl->error) {
            Logger::warning('IBPT.getImpostoOnline('.$curl->errorCode.') - '.$curl->errorMessage);
            $this->setOffline(true);
            return false;
        }
        $o = array(
            'importado' => $data->Importado,
            'nacional' => $data->Nacional,
            'estadual' => $data->Estadual,
            'municipal' => $data->Municipal,
            'tipo' => $data->Tipo
        );
        $vigenciainicio = date_create_from_format('d/m/Y', $data->VigenciaInicio);
        $vigenciafim = date_create_from_format('d/m/Y', $data->VigenciaFim);
        $info = array(
            'origem' => 'API IBPT',
            'fonte' => $data->Fonte,
            'versao' => $data->Versao,
            'chave' => $data->Chave,
            'vigencia' => array(
                'inicio' => date_format($vigenciainicio, 'Y-m-d'),
                'fim' => date_format($vigenciafim, 'Y-m-d')
            )
        );
        $o['info'] = $info;
        return $o;
    }

    public function getImposto($cnpj, $token, $ncm, $uf, $ex)
    {
        $uf = strtoupper($uf);
        $uf = preg_replace('/[^A-Z]/', '', $uf);
        if (is_null($cnpj) || is_null($token)) {
            return $this->getImpostoOffline($ncm, $uf, $ex);
        }
        $o = $this->getImpostoOnline($cnpj, $token, $ncm, $uf, $ex);
        if ($o === false) {
            return $this->getImpostoOffline($ncm, $uf, $ex);
        }
        return $o;
    }
}
