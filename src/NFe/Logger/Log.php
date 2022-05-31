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

namespace NFe\Logger;

/**
 * Salva mensagens de erro, depuração entre outros
 *
 * @author Francimar Alves <mazinsw@gmail.com>
 */
class Log
{
    /**
     * Pasta onde será salvo os arquivos de log
     *
     * @var string
     */
    private $directory;

    /**
     * Processador de log
     *
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Instância salva para uso estático
     *
     * @var Log
     */
    private static $instance;

    /**
     * Cria uma instância de Log
     * @param array $log campos para preencher o log
     */
    public function __construct($log = [])
    {
        $this->logger = new \Monolog\Logger('NFeAPI');
        $this->fromArray($log);
    }

    /**
     * Get current or create a new instance
     * @return Log current instance
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Pasta onde serão salvos os arquivos de Log
     * @return string diretório atual onde os logs são salvos
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Informa a nova pasta onde os logs serão salvos
     * @param string $directory caminho absoluto da pasta
     * @return Log a própria instência de Log
     */
    public function setDirectory($directory)
    {
        if ($this->directory == $directory) {
            return $this;
        }
        $this->directory = $directory;
        $this->setHandler(null);
        return $this;
    }

    /**
     * Altera o gerenciador que escreve os logs, informe null para restaurar o padrão
     *
     * @param \Monolog\Handler\AbstractHandler|null $handler nova função que será usada
     *
     * @return Log a própria instência de Log
     */
    public function setHandler($handler)
    {
        if ($handler === null) {
            $handler = new \Monolog\Handler\RotatingFileHandler($this->getDirectory() . '/{date}.txt');
            $handler->setFilenameFormat('{date}', 'Ymd');
        }
        $this->logger->pushHandler($handler);
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     *
     * @param bool $recursive
     *
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $log = [];
        $log['directory'] = $this->getDirectory();
        return $log;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param array|Log $log Array ou instância de Log, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($log = [])
    {
        if ($log instanceof Log) {
            $log = $log->toArray();
        } elseif (!is_array($log)) {
            return $this;
        }
        if (!isset($log['directory'])) {
            $this->setDirectory(dirname(dirname(dirname(__DIR__))) . '/storage/logs');
        } else {
            $this->setDirectory($log['directory']);
        }
        return $this;
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function error($message, $context = [])
    {
        self::getInstance()->logger->error($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function warning($message, $context = [])
    {
        self::getInstance()->logger->warning($message, $context);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function debug($message, $context = [])
    {
        self::getInstance()->logger->debug($message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function info($message, $context = [])
    {
        self::getInstance()->logger->info($message, $context);
    }
}
