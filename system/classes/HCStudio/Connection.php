<?php

/**
 * Derechos de autor por Hector Carrillo ( hector_o_c@hotmail.com )
 * Ultima actualizacion: 30/Ene/2015
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

use MySQLi;
use Exception;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class Connection
{
	# Variables privadas
	private static $connections = array(
		'default' => ['localhost', 'root', 'root', 'app_iam'],
		'world' => ['localhost', 'root', 'root', 'apps_world'],
		'blockchain' => ['localhost', 'root', 'root', 'app_blockchain']
	);

	private static $instances;

	static $instance;

	private $connection;
	private $debug;
	private $mysqli;
	const protocol = 'http';
	// const $proyect_url = '192.168.100.237:8888/mizuum';
	const proyect_url = 'localhost:8888/dummytraderweb';
	const proyect_name = 'DummieTrading';

	public function getConnectioName()
	{
		return $this->connection;
	}

	public static function getMainPath(): string
	{
		return self::protocol . "://" . self::proyect_url;
	}

	public static function getInstance()
	{
		if (!self::$instance instanceof self) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct($connection = null)
	{
		$this->setConnection($connection);
	}

	public function setConnection($connection = null)
	{
		# Setemos conexion por default
		$debug = Util::getVarFromPGS('debug');

		if (isset($debug) && $debug === 'true') {
			$connection = 'default_sandbox';
		} else {
			$connection = isset($connection) ? $connection : 'default';
		}

		# Si no se encuentra conexion
		if (!isset(self::$connections[$connection])) {
			throw new Exception('Conexion no localizada');
		} else {
			# Si no existe instancia, la creamos
			if (!isset(self::$instances[$connection])) {
				#
				list($host, $user, $pass, $db) = self::$connections[$connection];
				self::$instances[$connection] = new MySQLi($host, $user, $pass, $db);
				self::$instances[$connection]->query('SET NAMES "utf8"');
			}
			# Asignamos instancia en mysqli
			$this->mysqli = &self::$instances[$connection];
			#
			$this->connection = $connection;
		}
		# Debug si no esta en produccion
		if (__DEBUG__) {
			$this->debug = true;
		}
	}

	#
	public function __get($attribute)
	{
		return $this->mysqli->$attribute;
	}

	#
	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->mysqli, $method), $arguments);
	}

	#
	private function __clone()
	{
	}

	# upgrade to PHP 8.
	private function getType($item = null)
	{
		return match(gettype($item)) {
			'string' => 's',
			'boolean' => '',
			'integer' => 'i',
			'blob' => 'b',
			'double' => 'd',
			default => ''
		};
	}

	#
	public function stmtQuery($query, $bindParams = null)
	{
		# Sentencia preparada
		if ($stmt = $this->mysqli->prepare($query)) {
			# Si existen parametros a hacer 'bind'
			if (isset($bindParams) && !empty($bindParams)) {
				# Si no es arreglo
				if (!is_array($bindParams)) $bindParams = array($bindParams);
				#
				$refParams = [
					'_types_' => null
				];

				foreach ($bindParams as $key => &$param) {
					$refParams['_types_'] .= $this->getType($param);
					$refParams[$key] = &$param;
				}
				
				
				$stmt->bind_param(...array_values($refParams));
			}

			$stmt->execute();

			# Si existe error en query
			if ($stmt->errno) {
				throw new Exception($stmt->error);
			}
			
			# Retornamos 'true' en INSERT, UPDATE y DELETE
			if ($stmt->affected_rows > -1) {
				return true;
			}

			# Retornamos resultados en SELECT
			$results = [];
			#
			$metadata = $stmt->result_metadata();

			while ($field = $metadata->fetch_field()) {
				$row[$field->name] = &$row[$field->name];
			}

			$tmp = [];

			foreach ($row as $key => $value) $tmp[$key] = &$row[$key];

			$stmt->bind_result(...array_values($tmp));

			while ($stmt->fetch()) {
				$results[] = unserialize(serialize($row));
			}

			$stmt->close();
			
			#
			return empty($results) ? false : $results;
		} else {
			throw new Exception($this->mysqli->error);
		}
	}

	public function refValues($arr)
	{
		if (strnatcmp(phpversion(), '5.3') >= 0) {
			$refs = [];
			foreach ($arr as $key => $value)
				$refs[$key] = &$arr[$key];

			return $refs;
		}

		return $arr;
	}

	#
	public function rows($query, $bindParams = null)
	{
		return $this->stmtQuery($query, $bindParams);
	}

	#
	public function row($query, $bindParams = null)
	{
		$r = $this->stmtQuery($query, $bindParams);
		return ($r) ? current($r) : false;
	}

	#
	public function field($query, $bindParams = null)
	{
		$r = $this->row($query, $bindParams);
		return ($r) ? current($r) : false;
	}

	#
	public function column($query, $bindParams = null)
	{
		$r = $this->rows($query, $bindParams);
		if ($r) {
			$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($r));
			return iterator_to_array($iterator, false);
		}
		return false;
	}

	#
	public function insert($table, $fields)
	{
		$columns = implode(', ', array_keys($fields));
		$values = implode(',', array_fill(0, count($fields), '?'));
		$query = "INSERT INTO {$table}($columns) VALUES ($values)";
		$r = $this->stmtQuery($query, $fields);
		return ($r) ? $this->insert_id : false;
	}
}
