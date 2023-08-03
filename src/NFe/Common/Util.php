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

namespace NFe\Common;

/**
 * Utilitário para conversões de moeda, datas, verificação de dígitos, etc.
 */
class Util
{
    public const ACCENT_CHARS =
        'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ';
    public const NORMAL_CHARS =
        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY';

    /**
     * Converte float para string informando a quantidade de
     * casas decimais e usando ponto como separador
     * @param  float   $value  valor para ser convertido
     * @param  integer $places quantidade de casas decimais, padrão 2 casas
     * @return string          valor formatado
     */
    public static function toCurrency($value, $places = 2)
    {
        return number_format($value ?? 0, $places, '.', '');
    }

    /**
     * Converte float para string informando a quantidade de
     * casas decimais e usando ponto como separador
     * @param  float   $value  valor para ser convertido
     * @param  integer $places quantidade de casas decimais, padrão 4 casas
     * @return string          valor formatado
     */
    public static function toFloat($value, $places = 4)
    {
        return number_format($value ?? 0, $places, '.', '');
    }

    /**
     * Converte timestamp para data GMT
     * @param  integer $time data para ser convertida
     * @return string        data no formato GMT
     */
    public static function toDateTime($time)
    {
        return date('Y-m-d\TH:i:sP', $time);
    }

    /**
     * Converte uma cadeira de bytes para hexadecimal
     * @param  string $string cadeira de bytes ou de caracteres
     * @return string         representação em hexadecimal
     */
    public static function toHex($string)
    {
        $hexstr = unpack('H*', $string);
        return array_shift($hexstr);
    }

    /**
     * Adiciona zeros à esquerda para completar o comprimento
     * @param  string  $text  texto ou número a ser adicionados os zeros
     * @param  integer $len  quantidade de caracteres mínimo que deve ter
     * @param  string  $digit permite alterar o caractere a ser concatenado
     * @return string        texto com os zeros à esquerda
     */
    public static function padDigit($text, $len, $digit = '0')
    {
        return str_pad($text, $len, $digit, STR_PAD_LEFT);
    }

    /**
     * Adiciona zeros à direita para completar o comprimento
     * @param string $str texto ou número a ser adicionado os zeros
     * @param integer $len quantidade de caracteres mínimo
     * @param string  $txt caractere a ser adicionado quando não atingir
     * a quantidade len
     * @return string       texto com os zeros à direita
     */
    public static function padText($str, $len, $txt = '0')
    {
        return str_pad($str, $len, $txt, STR_PAD_RIGHT);
    }

    /**
     * Compara se dois valores flutuantes são iguais usando um delta como erro
     * @param  float  $value   valor a ser comparado
     * @param  float  $compare valor a ser comparado
     * @param  float   $delta   margem de erro para igualdade
     * @return boolean          true se for igual ou false caso contrário
     */
    public static function isEqual($value, $compare, $delta = 0.005)
    {
        return $compare < ($value + $delta) && ($value - $delta) < $compare;
    }

    /**
     * Compara se um valor é maior que outro usando um delta como erro
     * @param  float  $value   valor para testar se é maior
     * @param  float  $compare valor com que será comparado
     * @param  float   $delta   margem de erro para informar se é maior
     * @return boolean          true se o valor for maior ou false caso contrário
     */
    public static function isGreater($value, $compare, $delta = 0.005)
    {
        return $value > ($compare + $delta);
    }

    /**
     * Compara se um valor é menor que outro usando um delta como erro
     * @param  float  $value   valor a testar se é menor
     * @param  float  $compare valor com que comparar
     * @param  float   $delta   margem de erro para dizer se é menor
     * @return boolean          true se o valor for menor ou false caso contrário
     */
    public static function isLess($value, $compare, $delta = 0.005)
    {
        return ($value + $delta) < $compare;
    }

    /**
     * Converte um valor para a moeda Real já incluindo o símbolo
     * @param  float $value valor a ser formatado
     * @return string        valor já formatado e com o símbolo
     */
    public static function toMoney($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Realiza uma busca binária num array ordenado usando uma função customizada
     * para comparação
     * @param mixed $elem   elemento a ser procurado
     * @param array $array  array contendo todos os elementos
     * @param callable $cmp_fn função que irá comparar dois elementos
     * @return mixed retorna o valor do array referente a chave ou false caso não encontre
     */
    public static function binarySearch($elem, $array, $cmp_fn)
    {
        $bot = 0;
        $top = count($array) - 1;
        while ($top >= $bot) {
            $p = floor(($top + $bot) / 2);
            $o = $array[$p];
            $r = $cmp_fn($o, $elem);
            if ($r < 0) {
                $bot = $p + 1;
            } elseif ($r > 0) {
                $top = $p - 1;
            } else {
                return $o;
            }
        }
        return false;
    }

    /**
     * Remove acentos e caracteres especiais do texto
     * @param  string $text string com caracteres especiais
     * @return string      texto no formato ANSI sem caracteres especiais
     */
    public static function removeAccent($text)
    {
        if (! preg_match('/[\x80-\xff]/', $text)) {
            return $text;
        }

        $chars = [
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
        ];

        $text = strtr($text, $chars);

        return $text;
    }

    /**
     * Cria diretório com permissões
     * @param string $dir caminho da pasta a ser criada
     * @param int $access permissões da pasta
     */
    public static function createDirectory($dir, $access = 0711)
    {
        $oldUmask = umask(0);
        if (!file_exists($dir)) {
            mkdir($dir, $access, true);
        }
        umask($oldUmask);
    }

    /**
     * Retorna o módulo dos dígitos por 11
     * @param string $digitos dígitos para o cálculo
     * @return int            dígito do módulo 11
     */
    public static function getModulo11($digitos)
    {
        $sum = 0;
        $mul = 1;
        $len = strlen($digitos);
        for ($i = $len - 1; $i >= 0; $i--) {
            $mul++;
            $dig = intval($digitos[$i]);
            $sum += $dig * $mul;
            if ($mul == 9) {
                $mul = 1; // reset
            }
        }
        return $sum % 11;
    }

    /**
     * Retorna o módulo dos dígitos por 10
     * @param string $digitos dígitos para o cálculo
     * @return int            dígito do módulo 10
     */
    public static function getModulo10($digitos)
    {
        $sum = 0;
        $mul = 1;
        $len = strlen($digitos);
        for ($i = $len - 1; $i >= 0; $i--) {
            $mul++;
            $dig = intval($digitos[$i]);
            $term = $dig * $mul;
            $sum += ($dig == 9) ? $dig : ($term % 9);
            if ($mul == 2) {
                $mul = 0; // reset
            }
        }
        return $sum % 10;
    }

    /**
     * Retorna o Dígito de Auto-Conferência dos dígitos
     *
     * @param string $digitos
     * @param int $div Número divisor que determinará o resto da divisão
     * @param int $presente Informa o número padrão para substituição do excesso
     * @return int dígito verificador calculado
     */
    public static function getDAC($digitos, $div, $presente = 0)
    {
        $ext = $div % 10;
        if ($div == 10) {
            $ret = self::getModulo10($digitos);
        } else {
            $ret = self::getModulo11($digitos);
        }
        return ($ret <= $ext) ? $presente : ($div - $ret);
    }

    public static function appendNode($element, $name, $text, $before = null)
    {
        $dom = $element->ownerDocument;
        if (is_null($before)) {
            $node = $element->appendChild($dom->createElement($name));
        } else {
            $node = $element->insertBefore($dom->createElement($name), $before);
        }
        $node->appendChild($dom->createTextNode($text ?? ''));
        return $node;
    }

    public static function addAttribute($element, $name, $text)
    {
        $dom = $element->ownerDocument;
        $node = $element->appendChild($dom->createAttribute($name));
        $node->appendChild($dom->createTextNode($text ?? ''));
        return $node;
    }

    public static function loadNode($element, $name, $exception = null)
    {
        $value = null;
        $list = $element->getElementsByTagName($name);
        if ($list->length > 0) {
            $value = $list->item(0)->nodeValue;
        } elseif (!is_null($exception)) {
            throw new \Exception($exception, 404);
        }
        return $value;
    }

    public static function nodeExists($element, $name)
    {
        $list = $element->getElementsByTagName($name);
        return ($list->length > 0) || ($element->nodeName == $name);
    }

    public static function findNode($element, $name, $exception = null)
    {
        if ($element->nodeName == $name) {
            return $element;
        }
        $list = $element->getElementsByTagName($name);
        if ($list->length == 0) {
            if (is_null($exception)) {
                $exception = 'Node "' . $name . '" not found on element "' . $element->nodeName . '"';
            }
            throw new \Exception($exception, 404);
        }
        return $list->item(0);
    }

    public static function mergeNodes($element, $other)
    {
        $dom = $element->ownerDocument;
        foreach ($other->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $element->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                $element->replaceChild($node, $list->item(0));
            } else {
                $element->appendChild($node);
            }
        }
        return $element;
    }
}
