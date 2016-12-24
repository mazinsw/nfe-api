<?php
class Log {

	static private function write($type, $message) {
		$log_dir = dirname(dirname(__FILE__)).'/logs';
		$filename = $log_dir.'/'.date('Ymd').'.txt';
		$fp = fopen($filename, 'a');
		if(!$fp)
			return;
		fwrite($fp,date('d/m/Y H:i:s').' - '.$type.': '.$message."\n");
		fclose($fp);
		chmod($filename, 0755);
	}

	static public function error($message) {
		self::write('error', $message);
	}

	static public function warning($message) {
		self::write('warning', $message);
	}

	static public function debug($message) {
		self::write('debug', $message);
	}

	static public function information($message) {
		self::write('information', $message);
	}

}
