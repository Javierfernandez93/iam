<?php
/**
 * Derechos de autor por Hector Carrillo ( hector_o_c@hotmail.com )
 * Ultima actualizacion: 11/Mar/2015
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

use Exception;

#! Simple Object Relational Mapper
abstract class Orm
{
	private $_version = 1.2;
	protected $control = false;
	protected $tblName;
	protected $connection;
	private $tblPrimary;
	public $tblFields;
	private $db;

	#
	private static $dbObject;

	# Constructor
	public function __construct($connection = null)
	{
		# Obtenemos conexion
		$this->connection = (isset($connection)) ? $connection : 'default';
		$this->db = new Connection($this->connection);

		# Si no se definio tabla
		if (!$this->tblName) throw new Exception('No se encontro tabla para generar dataobject');

		
		# Si no existe dataobject
		if (!isset(self::$dbObject[$this->tblName]))
		{
			# Obtenemos informacion de tabla
			$dbSchema = Util::arr2obj($this->db->rows("SHOW COLUMNS FROM {$this->tblName}"));

			foreach ($dbSchema as $schema)  $this->tblFields[$schema->Field] = $this->getDefault($schema);

			# Guardamos dataobject
			self::$dbObject[$connection][$this->tblName] = $this->tblFields;

		} else {
			# Recuperamos dataobject
			$this->tblFields = self::$dbObject[$connection][$this->tblName];
		}
		# Asignamos llave primaria
		$this->tblPrimary = key($this->tblFields);
	}

	public function getFirst($order = 'ASC') {
		return $this->getOrder($order);
	}

	public function getLast($order = 'DESC') {
		return $this->getOrder($order);
	}

	private function getOrder($order = false) {
		return $this->db->stmtQuery("SELECT {$this->tblName}.{$this->tblPrimary} FROM {$this->tblName} ORDER BY {$this->tblName}.{$this->tblPrimary} {$order} LIMIT 1 ")[0][$this->tblPrimary];
	}

	public function setControl($control = false) {
		$this->control = $control;
	}
	# Metodo magico para setear atributos
	public function __set($attr, $val)
	{
		if ($attr != $this->tblPrimary && array_key_exists($attr, $this->tblFields)) $this->tblFields[$attr] = $val;
	}

	# Metodo magico para getear atributos
	public function __get($attr)
	{
		if ($attr != $this->tblPrimary && array_key_exists($attr, $this->tblFields)) return $this->tblFields[$attr];
	}

	# Obtenemos valor default en base a tipo de dato
	public function getTableName()
	{
		return $this->tblName;
	}
	private function getDefault($schema)
	{
		$this->checkValidSession();
		if ($schema->Extra == 'auto_increment') return 0;
		# Si tiene algo asignado por default
		if (!is_null($schema->Default))
			return ($schema->Default == 'CURRENT_TIMESTAMP' ? date('Y-m-d H:i:s') : $schema->Default);
		else {
			# Es nulo?
			if ($schema->Null == 'YES')
				return NULL;
			else {
				#
				$type = $schema->Type;
				if (strpos($type, '(') !== false)
					$type = substr($type, 0, strpos($type, '('));

				#
				if (in_array($type, array('varchar', 'text', 'char', 'tinytext', 'mediumtext', 'longtext', 'set', 'binary', 'varbinary', 'tinyblob', 'blob', 'mediumblob', 'longblob')))
					return '';
				else if ($type == 'datetime')
					return '0000-00-00 00:00:00';
				else if ($type == 'date')
					return '0000-00-00';
				else if ($type == 'time')
					return '00:00:00';
				else if ($type == 'year')
					return '0000';
				else if ($type == 'timestamp')
					return date('Y-m-d H:i:s');
				else if ($type == 'enum')
					return 1;
				else {  # Numerico
					return 0;
				}
			}
		}
	}

	# Obtenemos value de los campos
	private function getFields(array $filter = array())
	{
		return array_diff_key($this->tblFields, array_flip($filter));
	}

	public function truncate()
	{
		$this->db->stmtQuery("TRUNCATE TABLE {$this->tblName}");
	}

	#
	private function keyToLast($array)
	{
		array_push($array, array_shift($array));
		return $array;
	}

	# Obtenemos ID del objeto
	public function getId()
	{
		return $this->tblFields[$this->tblPrimary];
	}

	# Seteamos ID del objeto
	public function setId($id, $autoload = true)
	{
		if (is_numeric($id)) {
			$this->tblFields[$this->tblPrimary] = $id;
			if ($autoload) $this->cargar();
			return $this;
		}
	}

	# [Alias] Obtenemos lista de ID's
	public static function listar()
	{
		return self::listarDonde('');
	}

	# Lista de ID's por diversos parametros
	public static function listarDonde($where, $binds = null, $fieldName = false)
	{
		$class = get_called_class(); $obj = new $class;
		if($fieldName)
			$query = "SELECT {$fieldName} FROM {$obj->tblName}" . (!empty($where) ? " WHERE {$where}" : '');
		else
			$query = "SELECT {$obj->tblPrimary} FROM {$obj->tblName}" . (!empty($where) ? " WHERE {$where}" : '');
		$data = $obj->db->column($query, $binds);

		if ($data) return $data;

		return [];
	}

	# [Alias] Cargamos objeto por ID
	public function cargar()
	{
		return $this->cargarDonde("{$this->tblPrimary} = ?", $this->tblFields[$this->tblPrimary]);
	}

	# Cargamos objecto por diversos parametros
	public function loadWhere($where, $binds)
	{
		return $this->cargarDonde($where, $binds);
	}
	
	public function cargarDonde($where, $binds)
	{
		$query = "SELECT * FROM {$this->tblName} WHERE {$where} LIMIT 1";

		if($data = $this->db->row($query, $binds))
		{
			foreach($data as $k => $v) $this->tblFields[$k] = $v;
			return true;
		}
		return false;
	}

	#
	public static function contar()
	{
		return self::contarDonde('');
	}

	#
	public static function contarDonde($where, $binds = null)
	{
		$class = get_called_class(); $obj = new $class;
		$query = "SELECT count(*) FROM {$obj->tblName}" . ($binds ? " WHERE {$where}" : '');
		$data = $obj->db->field($query, $binds);

		if($data) return $data;

		return 0;
	}

	#
	public function cargarArray(array $array = null)
	{
		if(!isset($array)) 
		{
			return false;
		}

		if(isset($array[$this->tblPrimary]))
		{
			$this->setId($array[$this->tblPrimary]);
		}
		
		foreach ($array as $k => $v) {
			$this->{$k} = $v;
		}
	}

	public function loadArray(array $array = null)
	{
		$this->cargarArray($array);

		return $this;
	}

	public function saveNew()
	{
		$this->tblFields[$this->tblPrimary] = 0;
		$this->guardar();
	}
	public function save()
	{
		return $this->guardar();
	}

	# Guardamos/Actualizamos objecto
	public function guardar()
	{
		if (!$this->getId()) {
			$query = "INSERT INTO {$this->tblName} VALUES({$this->sqlHelper($this->getFields(), true)})";

			$data = $this->db->stmtQuery($query, $this->getFields());
			# control save
		} else {
			$query = "UPDATE {$this->tblName} SET {$this->sqlHelper($this->atributos())} WHERE {$this->tblPrimary} = ?";
			$data = $this->db->stmtQuery($query, $this->keyToLast($this->getFields()));
			# control update
		}

		if ($data)
			if (!$this->getId()) $this->setId($this->db->insert_id, false);
				return true;

		return false;
	}

	public function saveSql($query = null)
	{
		if (isset($query)) {
			$data = $this->db->stmtQuery($query);
		}

		if ($data)
			if (!$this->getId()) $this->setId($this->db->insert_id, false);
				return true;

		return false;
	}

	public function exist()
	{
		$first = true;
		foreach ($this->tblFields as $key => $value)
			if($key != $this->tblPrimary)
				if($first)
				{
					$where = " WHERE {$key} = '{$value}' ";
					$first = false;
				} else $where .= " AND {$key} = '{$value}' ";

		$sql = "SELECT {$this->tblPrimary} FROM {$this->tblName} {$where}";

		return ($this->connection()->field($sql)) ? true : false;
	}

	public function getVersion()
	{
		return $this->_version;
	}
	# Borramos registro en db
	public function borrar()
	{
		$query = "DELETE FROM {$this->tblName} WHERE {$this->tblPrimary} = ?";
		$data = $this->db->stmtQuery($query, $this->tblFields[$this->tblPrimary]);
		# control delete

		if($data) return true;

		return false;
	}

	# [Alias] Obtenemos value de los campos (exepto primario)
	public function checkValidSession()
	{
		if((new Session('support_user'))->get('pid'))
			return $this->control = true;

		return false;
	}
	//saca todo los atributos del objeto
	public function atributos(array $filter = array())
	{
		return $this->getFields(array_merge($filter, array($this->tblPrimary)));
	}

	public function attr(array $filter = array())
	{
		return $this->atributos($filter);
	}
	public function data(array $filter = array())
	{
		return array_merge([$this->tblPrimary=>$this->getId()],$this->atributos($filter));
	}
	public function JoinAtributos($array = array())
	{
		return $this->tblFields = array_merge($this->getFields(),$array);
	}
	#
	public function connection()
	{
		return $this->db;
	}

	# "a = ?, b = ?, c = ?" OR "?, ?, ?"
	public static function sqlHelper(array $fields, $onlyQmark = false)
	{
		if ($onlyQmark)
			return implode(',', array_fill(0, count($fields), '?'));

		return implode(' = ?, ', array_keys($fields)).' = ?';
	}

	# "a = ? AND b = ? AND c = ?"
	public static function sqlHelperAND(array $fields)
	{
		return str_replace(',', ' AND', self::sqlHelper($fields));
	}

	# "a = ? OR b = ? OR c = ?"
	public static function sqlHelperOR(array $fields)
	{
		return str_replace(',', ' OR', self::sqlHelper($fields));
	}
	
	public function updateField(string $field = null,int|string|float $value = null) 
	{
		if(!$this->getId())
		{
			return false;
		}

		$this->{$field} = $value;

		return $this->save();
	}

	public function updateStatus(string|int $status = null) 
	{
		return $this->updateField("status",$status);
	}

	public function active() 
	{
		return $this->updateField("status",'1');
	}
	
	public function unActive() 
	{
		return $this->updateField("status",'0');
	}
	
	public function delete() 
	{
		return $this->updateField("status",'-1');
	}

	public function touch() : bool
	{
		if(!isset($this->create_date))
		{
			return false;
		}

		$this->create_date = time();
	
		return $this->save();
	}

	public function find(int $id = null) 
	{
		return $this->where("{$this->tblName}_id",'=',$id);
	}

	public function whereRaw(string $query = null) 
	{
		if($data = $this->db->row($query, null))
		{
			foreach($data as $k => $v) $this->tblFields[$k] = $v;

			return $this;
		}

		return false;	
	}

	public function where(string $field = null,string $comparation = null,string|float|int $value = null) 
	{
		return $this->whereRaw("SELECT * FROM {$this->tblName} WHERE {$this->tblName}.{$field} {$comparation} '{$value}' LIMIT 1");
	}
	
	public function allByOrdered(string $where = null,array|string|int|float $binds = null,array $fields = null,array $orderBy = null) : array|bool
	{
		return $this->findAll($where,$binds,null,['field'=>'create_date','order'=>'desc']);
	}

	public function findAll(string $where = null,array|string|int|float $binds = null,array $fields = null,array $orderBy = null) : array|bool
	{
		$fields = isset($fields) ? implode(',',$fields) : implode(",",array_keys($this->getFields()));

		$query = "SELECT {$fields} FROM {$this->tblName} WHERE {$where}";

		if(isset($orderBy))
		{
			$query .= " ORDER BY {$orderBy['field']} ".strtoupper($orderBy['order']);
		}

		if($data = $this->db->rows($query, $binds))
		{
			return $data;
		}

		return false;
	}
	
	public function findRow(string $where = null,array|string|int|float $binds = null,array $fields = null) : array|bool
	{
		$fields = isset($fields) ? implode(',',$fields) : implode(",",array_keys($this->getFields()));

		$query = "SELECT {$fields} FROM {$this->tblName} WHERE {$where}";

		if($data = $this->db->row($query, $binds))
		{
			return $data;
		}

		return false;
	}
}