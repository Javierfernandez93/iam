<?php

/**
 * Derechos de autor por Hector Carrillo ( hector_o_c@hotmail.com )
 * Ultima actualizacion: 23/Ene/2015
 *
 * Autorizado en virtud de la Licencia de Apache, Versión 2.0 (la "Licencia");
 * se prohíbe utilizar este archivo excepto en cumplimiento de la Licencia.
 * Podrá obtener una copia de la Licencia en:
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * A menos que lo exijan las leyes pertinentes o se haya establecido por escrito,
 * el software distribuido en virtud de la Licencia se distribuye “TAL CUAL”, SIN
 * GARANTÍAS NI CONDICIONES DE NINGÚN TIPO, ya sean expresas o implícitas. Véase
 * la Licencia para consultar el texto específico relativo a los permisos y
 * limitaciones establecidos en la Licencia.
 */

namespace HCStudio;

class Util
{
	public static $username_streaming = "55A332A001N";
	public static $password_streaming = "99e01032Axx";
	public static $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
	public static $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

	const USERNAME = "1922@FUNNELS07";
	const PASSWORD = "@FUNNELS07@";

	public static $STRING_LENGHT = 80;
	private function __construct()
	{
		# Nada que hacer ...
	}
	public static function getDateSimple($date = null)
	{
		return date("d", $date) . " de " . self::getMonthById(date("m", $date) - 1) . " " . date("Y", $date);
	}
	private function __clone()
	{
		# Nada que hacer ...
	}
	public static function getNumbersWithFloat(string $string = null)
	{
		if (preg_match('!\d+\.*\d*!', $string, $matches)) { 
			return $matches[0]; //returning the first match 
		}
	}

	# Dump de variables
	public static function getNumbers(string $phone = null)
	{
		return (int) filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
	}

	public static function getDate($date = null)
	{
		$date = isset($date) ? $date : time();
		return date("H:m", $date) . " el " . date("d", $date) . " de " . self::getMonthById(date("m", $date) - 1) . " del " . date("Y", $date);
	}

	public static function getDateSimples($date = null)
	{
		$date = isset($date) ? $date : time();
		return date("d", $date) . " " . self::getMonthById(date("m", $date) - 1);
	}

	public static function getUtilDate($date = null)
	{
		return self::getDayById(date("w", $date) - 1) . " " . date("d", $date) . " " . self::getMonthById(date("m", $date) - 1);
	}

	public static function getMonthById($month_id = null)
	{
		return self::$months[$month_id];
	}

	public static function getDayById($day_id = null)
	{
		return self::$days[$day_id];
	}

	public static function dump($val)
	{
		echo '<pre>' . htmlspecialchars(print_r($val, true)) . '</pre>';
	}

	public static function _getPercentaje($max = null, $items = null)
	{
		return round(($items * 100) / $max);
	}

	public static function getPercentaje($amount = null, $percentaje = null, $rest = false)
	{
		$result = false;

		if (isset($amount, $percentaje) === true) {
			$result = ($percentaje * $amount) / 100;

			if ($rest === true) {
				$result = $amount - $result;
			}
		}

		return $result;
	}
	# Dump de variables definidas en script
	public static function dumpLocals()
	{
		$return = array();
		$exclude = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', '_SESSION');
		foreach ($GLOBALS as $key => $val) if (!in_array($key, $exclude) && !is_object($val)) $return[$key] = $val;
		self::dump($return);
	}
	public static function getAcronime($string = null)
	{
		$words = explode(" ", $string);

		foreach ($words as $word) {
			$acronime .= strtoupper($word[0]);
		}

		return $acronime;
	}


	# Asignamos valores de arreglo en variables propias
	public static function pullOut(array $arr, $prefx = 'x')
	{
		foreach ($arr as $k => $v) $GLOBALS[$prefx . $k] = $v;
	}
	# Convertimos arreglo en objeto
	public static function arr2obj(array $array = null)
	{
		if(isset($array))
		{
			$json = json_encode($array);
			$object = json_decode($json);

			return $object;
		}

		return null;
	}

	# Convertimos objeto en arreglo
	public static function obj2arr(array $array)
	{
		$json = json_encode($object);
		$array = json_decode($json, true);
		return $array;
	}

	# Devuelve nombre de script ejecutado
	public static function getFile()
	{
		return basename($_SERVER['SCRIPT_NAME'], '.php');
	}

	public static function reArrayFiles(&$file_post)
	{
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
		for ($i = 0; $i < $file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
		return $file_ary;
	}

	public static function createSlug($str, $max = 30)
	{
		$out = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$out = substr(preg_replace("/[^-\/+|\w ]/", '', $out), 0, $max);
		$out = trim($out, '-');
		$out = preg_replace("/[\/_| -]+/", '-', $out);
		return $out;
	}

	public static function checkSlug($str)
	{
		return preg_match("/^[a-z0-9\-]*$/", $str);
	}

	public static function getIP()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	public static function getIsset(&$v)
	{
		return (isset($v) ? $v : '');
	}

	public static function checkCurlAuthHeaders($_username = false, $_password = false)
	{
		return self::getAuth() === base64_encode("{$_username}:$_password");
	}

	public static function checkCurlAuthHeadersStreaming($_username_streaming = false, $_password_streaming = false)
	{
		return self::getAuthStreaming() === base64_encode("{$_username_streaming}:$_password_streaming");
	}

	public static function getAuth()
	{
		return base64_encode(self::$username . ":" . self::$password);
	}

	public static function replaceQuery($string)
	{
		return str_replace("0X", "0", $string);
	}

	public static function getAuthStreaming()
	{
		return base64_encode(self::$username_streaming . ":" . self::$password_streaming);
	}

	public static function doCurl($url = null, $params = [], $encode = true)
	{
		if (isset($url, $params) && is_array($params)) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url . "?" . self::replaceQuery(http_build_query($params)),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_USERPWD => self::getAuth(),
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Basic " . self::getAuth(),
					"Content-Type: application/json",
					"cache-control: no-cache"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return '<n style="color:red"> WARNING ! <n><br>' . $err;
				// echo '<n style="color:red"> WARNING ! <n><br>' . $err;
			} else {
				return json_decode($response, $encode);
			}
		}

		return false;
	}
	public static function doRequest($url, $post = '', $data = '', $json = false)
	{
		if (!empty($post) && $post == 'POST') {
			$params = array('http' => array(
				'method' => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			));
		} else {
			$params = array('http' => array(
				'method' => 'GET'
			));
		}
		$context = stream_context_create($params);
		$response = file_get_contents($url, false, $context);
		return ($json) ? json_decode($response) : $response;
	}

	public static function word2unicode($word)
	{
		$p = str_split(trim($word));
		$new_word = '';
		foreach ($p as $val) {
			$new_word .= '&#' . ord($val) . ';';
		}
		return $new_word;
	}
	# Formatea cantida a modo currency
	public static function currencyFormat($num)
	{
		return '$' . number_format($num, 2);
	}
	public static function redirectTo(string $url, array $params = null)
	{
		$query = isset($params) ? "?" . http_build_query($params) : '';
		header("location: {$url}{$query}");
		die();
	}

	public static function getVarFromPGS($var = null, $session_included = true, $server_auth_included = false)
	{
		if (isset($var) === true) {
			$data = (isset($_POST[$var])) ? $_POST[$var] : false;
			$data = (isset($_GET[$var])) ? $_GET[$var] : $data;

			if ($session_included) $data = (isset($_SESSION[$var])) ? $_SESSION[$var] : $data;
		} else {
			$data = (isset($_POST)) ? $_POST : false;
			$data = (isset($_GET) && !$data) ? $_GET : $data;

			if ($session_included) $data = (isset($_SESSION) && !$data) ? $_SESSION : $data;
		}

		if ($server_auth_included === true) {
			if (isset($_SERVER["PHP_AUTH_USER"])) {
				$data["PHP_AUTH_USER"] = $_SERVER["PHP_AUTH_USER"];
			}
			if (isset($_SERVER["PHP_AUTH_PW"])) {
				$data["PHP_AUTH_PW"] = $_SERVER["PHP_AUTH_PW"];
			}
		}

		return $data;
	}
	public static function sanitizeString($string, $special_chars = false, $delete_space = true)
	{
		$string = trim($string);

		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);

		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);

		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);

		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);

		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);

		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);

		if ($special_chars) {
			$arr = ["\\", "¨", "º", "-", "~", "#", "@", "|", "!", "\"", "·", "$", "%", "&", "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "]", "+", "}", "{", "¨", "´", ">", "< ", ";", ",", ":", "."];
			if ($delete_space) {
				$arr[] =  " ";
			}

			$string = str_replace($arr, '', $string);
		}

		return $string;
	}

	public static function unsetField($data, $field)
	{
		if (isset($data[$field])) {
			unset($data[$field]);
		}
		return $data;
	}

	public static function compressDataForPhone($data)
	{
		$data = self::unsetField($data, "email");
		$data = self::unsetField($data, "password");
		$data = self::unsetField($data, "company_id");
		$data = self::unsetField($data, "gzip");

		return $data;
	}

	public static function compressDataForSoftware($data = null)
	{
		$data = self::unsetField($data, "email");
		$data = self::unsetField($data, "password");
		$data = self::unsetField($data, "company_id");
		$data = self::unsetField($data, "gzip");

		if (isset($data['for_mac']) === true) {
			if (isset($data['Data'])) {
				$data = array_merge($data, $data['Data']);
				unset($data['Data']);
			}
		} else {
			$_data = $data;
			$data = null;
			$data['Data'] = $_data;
			$data['s'] = $_data['s'];
			$data['r'] = $_data['r'];
		}

		return $data;
	}

	public static function compressDataForApi($data)
	{
		$data = self::unsetField($data, "secret");
		$data = self::unsetField($data, "token");
		$data = self::unsetField($data, "gzip");

		return $data;
	}

	public static function getHeadersForWebService(bool $session_included = false, bool $server_auth_included = false, bool $json_included = true)
	{
		if ($json_included === true) {
			self::getHeadersForAllDevices();
		}

		$data = self::getVarFromPGS(null, $session_included, $server_auth_included);
		$data["gzip"] = self::getCompressor();

		return $data;
	}

	public static function getCompressor()
	{
		if (!ob_start("ob_gzhandler")) {
			ob_start();
			return false;
		} else return true;
	}

	public static function getDiffDaysFormat($date = null)
	{
		$days = self::getDiffDays($date);

		if ($days > 1) {
			return ceil($days);
		} else if ($days > 0 && $days < 1) {
			return 0;
		}

		return -1;
	}

	public static function getDiffDays($date = null)
	{
		return ($date - time()) / (60 * 60 * 24);
	}

	public static function timeAgoHM($ptime = false)
	{
		$diff = time() - $ptime;
		$calc_times = array();
		$timeleft   = array();

		$calc_times[] = array('hora', 'horas', 3600);
		$calc_times[] = array('minuto', 'minutos', 60);

		foreach ($calc_times as $timedata) {
			list($time_sing, $time_plur, $offset) = $timedata;

			if ($diff >= $offset) {
				$left = floor($diff / $offset);
				$diff -= ($left * $offset);

				if ($time_sing == "hora" || $time_sing == "horas") {
					if ($left >= 1) {
						$timeleft = "Hace {$left} " . ($left == 1 ? $time_sing : $time_plur);
					}
				} else {
					if ($left > 60) {
						$timeleft = "Hace {$left} " . ($left == 1 ? $time_sing : $time_plur);
					}
				}
			}
		}

		return $timeleft;
	}

	public static function unixDiff(int $date1 = null,int $date2 = null) : array
	{
		$diff = $date2 - $date1; 

		$years = floor($diff / (365*60*60*24)); 
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
		$minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
		$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60)); 
		
		return [
			'years' => $years,
			'months' => $months,
			'hours' => $hours,
			'days' => $days,
			'minutes' => $minutes,
			'seconds' => $seconds,
		];
	}
	
	public static function timeAgoIO($ptime = false)
	{
		$diff = time() - $ptime;
		$calc_times = array();
		$timeleft   = array();

		$calc_times[] = array('día', 'dias', 86400);
		$calc_times[] = array('hora', 'horas', 3600);
		$calc_times[] = array('minuto', 'minutos', 60);

		foreach ($calc_times as $timedata) {
			list($time_sing, $time_plur, $offset) = $timedata;

			if ($diff >= $offset) {
				$left = floor($diff / $offset);
				$diff -= ($left * $offset);

				if ($time_sing == "día") {
					if ($left >= 10) {
						$timeleft = [];
						return $timeleft[] = "más de 10 {$time_plur}";
					}
				}

				$timeleft[] = "{$left} " . ($left == 1 ? $time_sing : $time_plur);
			}
		}

		return $timeleft ? (time() > $ptime ? null : '-') . implode(' ', $timeleft) : 0;
	}

	public static function timeDiffIO($stime = false, $etime = false)
	{
		$diff = $stime - $etime;
		$calc_times = array();
		$timeleft   = array();

		$calc_times[] = array('día', 'dias', 86400);
		$calc_times[] = array('hora', 'horas', 3600);
		$calc_times[] = array('minuto', 'minutos', 60);

		foreach ($calc_times as $timedata) {
			list($time_sing, $time_plur, $offset) = $timedata;

			if ($diff >= $offset) {
				$left = floor($diff / $offset);
				$diff -= ($left * $offset);

				if ($time_sing == "día") {
					if ($left >= 10) {
						$timeleft = [];
						return $timeleft[] = "más de 10 {$time_plur}";
					}
				}

				$timeleft[] = "{$left} " . ($left == 1 ? $time_sing : $time_plur);
			}
		}

		return $timeleft ? (time() > $ptime ? null : '-') . implode(' ', $timeleft) : 0;
	}

	public static function timeDiffHM($stime = false, $etime = false)
	{
		$diff = $stime - $etime;
		$calc_times = array();
		$timeleft   = array();

		$calc_times[] = array('hora', 'horas', 3600);
		$calc_times[] = array('minuto', 'minutos', 60);

		foreach ($calc_times as $timedata) {
			list($time_sing, $time_plur, $offset) = $timedata;

			if ($diff >= $offset) {
				$left = floor($diff / $offset);
				$diff -= ($left * $offset);

				$timeleft[] = "{$left}";
			} else {
				$timeleft[] = "0";
			}
		}

		return $timeleft;
	}

	public static function timeAgo($ptime = false)
	{
		$diff = time() - $ptime;
		$calc_times = array();
		$timeleft   = array();

		$calc_times[] = array('año',   'años',   31557600);
		$calc_times[] = array('mes',  'meses',  2592000);
		$calc_times[] = array('día',    'dias',    86400);
		$calc_times[] = array('hora',   'horas',   3600);
		$calc_times[] = array('minuto', 'minutos', 60);
		// $calc_times[] = array('segundo', 'segundos', 1);

		foreach ($calc_times as $timedata) {
			list($time_sing, $time_plur, $offset) = $timedata;

			if ($diff >= $offset) {
				$left = floor($diff / $offset);
				$diff -= ($left * $offset);
				$timeleft[] = "{$left} " . ($left == 1 ? $time_sing : $time_plur);
			}
		}

		return $timeleft ? (time() > $ptime ? null : '-') . implode(' ', $timeleft) : 0;
	}
	public static function timeDiff($stime = false, $etime = false)
	{
		$diff = $stime - $etime;
		$calc_times = array();
		$timeleft   = array();

		// $calc_times[] = array('año',   'años',   31557600);
		// $calc_times[] = array('mes',  'meses',  2592000);
		// $calc_times[] = array('día',    'dias',    86400);
		// $calc_times[] = array('hora',   'horas',   3600);
		$calc_times[] = array('minuto', 'minutos', 60);
		// $calc_times[] = array('segundo', 'segundos', 1);

		foreach ($calc_times as $timedata) {
			list($time_sing, $time_plur, $offset) = $timedata;

			if ($diff >= $offset) {
				$left = floor($diff / $offset);
				$diff -= ($left * $offset);
				$timeleft[] = "{$left}";
			}
		}

		return $timeleft ? (time() > $ptime ? null : '-') . implode(' ', $timeleft) : 0;
	}

	public static function getServerPath()
	{
		return Connection::$protocol . "://" . Connection::$proyect_url;
	}
	public static function getHeadersForAllDevices()
	{
		set_time_limit(0);
		ini_set("memory_limit", "1024M");
		ini_set("allow_url_fopen", true);

		header("Content-Type: application/json");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) $_POST = json_decode(file_get_contents('php://input'), true);
	}
	public static function debug($var = null)
	{
		if (isset($var)) {
			if ($debug = self::getVarFromPGS("debug")) {
				echo "<pre>";
				print_r($var);
				die;
			}
		}
	}

	public static function countDigits($str = null)
	{
		preg_match_all('!\d+!', $str, $numbers);

		if (isset($numbers[0])) return $numbers[0][0];
	}

	public static function formatBytes($bytes = null, $precision = 2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	public static function getCurrentURL()
	{
		$currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		$currentURL .= $_SERVER["SERVER_NAME"];

		if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
			$currentURL .= ":" . $_SERVER["SERVER_PORT"];
		}

		$currentURL .= $_SERVER["REQUEST_URI"];
		return $currentURL;
	}
	public static function sanitizeStringLenght($string = null)
	{
		if (strlen($string) > self::$STRING_LENGHT) {
			return substr($string, 0, self::$STRING_LENGHT - 1) . "...";
		}

		return $string;
	}

	public static function isValidPhone(string $phone, int $minDigits = 9, int $maxDigits = 14)
	{
		return preg_match('/^[0-9]{' . $minDigits . ',' . $maxDigits . '}\z/', $phone) ? true : false;
	}

	public static function formatSizeUnits($bytes)
	{
		if ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		} elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		} elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		} elseif ($bytes > 1) {
			$bytes = $bytes . ' bytes';
		} elseif ($bytes == 1) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	public static function isJson(string $string = null) : bool {
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	 }
}
