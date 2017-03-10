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
namespace NFe\Log;

/**
 * Salva mensagens de erro, depuração entre outros
 */
class Logger
{
    private $directory;
    private $write_function;
    private static $instance;

    public function __construct($logger = array())
    {
        $this->fromArray($logger);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Pasta onde serão salvos os arquivos de Log
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Altera a função que escreve os logs, informe null para restaurar o padrão
     * @param function $write_function nova função que será usada
     */
    public function setWriteFunction($write_function)
    {
        $this->write_function = $write_function;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $logger = array();
        $logger['directory'] = $this->getDirectory();
        return $logger;
    }

    public function fromArray($logger = array())
    {
        if ($logger instanceof Logger) {
            $logger = $logger->toArray();
        } elseif (!is_array($logger)) {
            return $this;
        }
        if (!isset($logger['directory'])) {
            $this->setDirectory(dirname(dirname(dirname(__DIR__))).'/docs/logs');
        } else {
            $this->setDirectory($logger['directory']);
        }
        return $this;
    }

    private function writeLog($type, $message)
    {
        $filename = $this->getDirectory().'/'.date('Ymd').'.txt';
        $fp = @fopen($filename, 'a');
        if (!$fp) {
            throw new \Exception('Não foi possível abrir o arquivo de log "'.$filename.'"', 500);
        }
        fwrite($fp, date('d/m/Y H:i:s').' - '.$type.': '.$message."\n");
        fclose($fp);
        chmod($filename, 0755);
    }

    private function write($type, $message)
    {
        if (!is_null($this->write_function)) {
            call_user_func_array($this->write_function, func_get_args());
        } else {
            $this->writeLog($type, $message);
        }
    }

    private function errorLog($message)
    {
        $this->write('error', $message);
    }

    private function warningLog($message)
    {
        $this->write('warning', $message);
    }

    private function debugLog($message)
    {
        $this->write('debug', $message);
    }

    private function informationLog($message)
    {
        $this->write('information', $message);
    }

    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'error':
            case 'warning':
            case 'debug':
            case 'information':
                return call_user_func_array(array($this, $name . 'Log'), $arguments);
            default:
                throw new \BadMethodCallException(
                    sprintf('Call to undefined function: %s->%s().', get_class($this), $name),
                    500
                );
        }
    }

    public static function __callStatic($name, $arguments)
    {
        switch ($name) {
            case 'error':
            case 'warning':
            case 'debug':
            case 'information':
                return call_user_func_array(array(self::getInstance(), $name . 'Log'), $arguments);
            default:
                throw new \BadMethodCallException(
                    sprintf('Call to undefined function: %s::%s().', __CLASS__, $name),
                    500
                );
        }
    }
}
