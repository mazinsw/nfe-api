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

class Log {

	private static function write($type, $message) {
		$log_dir = dirname(dirname(__FILE__)).'/logs';
		$filename = $log_dir.'/'.date('Ymd').'.txt';
		$fp = fopen($filename, 'a');
		if(!$fp)
			return;
		fwrite($fp,date('d/m/Y H:i:s').' - '.$type.': '.$message."\n");
		fclose($fp);
		chmod($filename, 0755);
	}

	public static function error($message) {
		self::write('error', $message);
	}

	public static function warning($message) {
		self::write('warning', $message);
	}

	public static function debug($message) {
		self::write('debug', $message);
	}

	public static function information($message) {
		self::write('information', $message);
	}

}
