<?php
namespace Cy;

use PDO;

class CyDB extends PDO
{
	private $config = null;
	public $table;   //当前操作的数据表名字
	protected $fields;
	private $where = array();
	private $select = '*';
	public $sql;
	public $msg;
	private $stmt; //PDOStatement实例
	private $param = array();

	/**
	 * 初始化一个数据库实例
	 * @return db
	 */
	public function __construct()
	{
		$this->config['dsn'] = \Cy\CyConfig::getConfig('DataBase', 'DSN');
		$this->config['user'] = \Cy\CyConfig::getConfig('DataBase', 'USER');
		$this->config['pass'] = \Cy\CyConfig::getConfig('DataBase', 'PASSWD');
		$this->config['options'] = \Cy\CyConfig::getConfig('DataBase', 'OPTIONS');
		try {
			parent::__construct($this->config['dsn'], $this->config['user'], $this->config['pass'], $this->config['options']);
		} catch (\PDOException $e) {
			throw new \Exception('数据库连接失败' . $e->getMessage());
		}

	}

	/**
	 * 重建连接
	 */
	public function reConnect()
	{
		$attr = array();
		foreach ($this as $k => $v) {
			$attr[$k] = $v;
		}
		parent::__construct($this->config['dsn'], $this->config['user'], $this->config['pass'], $this->config['options']);
		foreach ($attr as $k => $v) {
			$this->$k = $v;
		}
	}

	/**
	 * 插入数据
	 * @param array $data
	 */
	public function insert($data = array())
	{
		$this->sql = sprintf('INSERT INTO `%s` SET %s', $this->table, implode(',', $this->joinKeyVal($data)));
		if (!$this->query($this->sql)) {
			return false;
		}
		return $this->lastInsertId();
	}

	/**
	 * 插入数据，当出现key重复时更新数据
	 * INSERT ... tbl_name SET ... ON DUPLICATE KEY UPDATE ...
	 * @param array $insertData 不存在记录时的新增数据
	 * @param array $updateData 存在记录时的更新数据
	 * @return int 新增记录id
	 */
	public function insertDuplicate(array $insertData, array $updateData)
	{
		$this->sql = sprintf('INSERT INTO %s SET %s ON DUPLICATE KEY UPDATE %s', $this->table,
			implode(',', $this->joinKeyVal($insertData)), implode(',', $this->joinKeyVal($updateData)));
		if (!$this->query($this->sql)) {
			return false;
		}
		return $this->lastInsertId();
	}

	/**
	 * 计算有多少条记录
	 */
	public function count($strCountSql = 'count(*)')
	{
		$this->sql = sprintf('SELECT %s FROM %s %s', $strCountSql, $this->table, $this->condition());
		$sth = $this->query($this->sql);
		$res = $sth->fetchColumn(0);
		return (int)$res;
	}

	/**
	 * @param $sql
	 * @param int $type
	 * @param int $returnType 1 默认（sql错误时返回空数组，没查到数据时返回false） ，2 （sql错误时返回 false，没查到数据时返回空数组）
	 * @return array|bool|mixed
	 */
	public function row($sql, $type = PDO::FETCH_ASSOC, $returnType = 1)
	{
		$this->sql = $sql;
		$sth = $this->query($this->sql);
		if (!$sth) {
			return $returnType == 1 ? array() : false;
		}
		$res = $sth->fetch($type);
		return ($returnType == 1 || !empty($res)) ? $res : array();
	}

	public function rows($sql, $start = 0, $size = 0, $orderBy = '', $groupBy = '', $type = PDO::FETCH_ASSOC)
	{
		$this->sql = $sql;
		if ($groupBy) {
			$this->sql = sprintf('%s GROUP BY %s', $this->sql, $groupBy);
		}
		if ($orderBy) {
			$this->sql = sprintf('%s ORDER BY %s', $this->sql, $orderBy);
		}
		if ($size > 0) {
			//$this->sql = sprintf('%s LIMIT %d,%d', $this->sql, $start, ($size>1000?1000:$size));
			$this->sql = sprintf('%s LIMIT %d,%d', $this->sql, $start, $size);
		}
		$sth = $this->query($this->sql);
		if (!$sth) {
			return array();
		}
		return $sth->fetchAll($type);
	}

	/**
	 * 更新数据
	 * @param array $data
	 * 返回受影响的行数/出错返回false
	 */
	public function update($data = array())
	{
		$this->sql = sprintf('UPDATE %s SET %s %s', $this->table, implode(',', $this->joinKeyVal($data)), $this->condition());
		return $this->exec($this->sql);
	}

	/**
	 * 根据查询条件返回一条记录
	 * @param int $returnType 参见 $this->row() 注释
	 * @return array|mixed
	 */
	public function find($returnType = 1)
	{
		$this->sql = 'SELECT ' . $this->select . ' FROM ' . $this->table . $this->condition();
		return $this->row($this->sql, PDO::FETCH_ASSOC, $returnType);
	}

	/**
	 * 根据查询条件返回多条记录
	 */
	public function findAll($offset = 0, $limit = 0, $orderBy = '', $groupBy = '')
	{
		$this->sql = 'SELECT ' . $this->select . ' FROM ' . $this->table . $this->condition();
		return $this->rows($this->sql, $offset, $limit, $orderBy, $groupBy);
	}

	/**
	 * 删除数据
	 */
	public function delete()
	{
		$this->sql = 'DELETE FROM ' . $this->table . $this->condition();
		$ret = $this->exec($this->sql);
		return $ret !== false;
	}

	/**
	 * 设置各种查询条件
	 * @param mixed $input
	 */
	public function where(array $input)
	{
		$this->where = array_merge($this->where, $input);
		return $this;
	}


	/**
	 * WHERE array 转换 SQL字符串
	 * @return string
	 */
	public function condition()
	{
		$condStr = '';
		if (!count($this->where)) return $condStr;
		$condStr = ' WHERE ';
		foreach ($this->where as $filed => $value) {
			if (is_array($value)) {
				foreach ($value as $operator => $operatorValue) {
					$operator = strtoupper($operator);
					if (is_array($operatorValue)) { //IN NOT IN 数组条件
						$operatorValue = array_map(function ($mapValue) {
							return $this->quote($mapValue);
						}, $operatorValue);
						$operatorValue = implode(',', $operatorValue);
						$condStr .= '`' . $filed . '` ' . $operator . ' (' . $operatorValue . ') AND ';
					} else {
						//LIKE <= >= < > <>等符号
						$operatorValue = $this->quote($operatorValue);
						$condStr .= '`' . $filed . '` ' . $operator . ' ' . $operatorValue . ' AND ';
					}
				}
			} else {
				// = 号
				$value = $this->quote($value);
				$condStr .= "`$filed` = $value AND ";
			}
		}
		//消除 AND
		return substr($condStr, 0, -4);
	}

	public function select($fields)
	{
		if (is_array($fields) && $fields) {
			$this->select = '`' . implode('`,`', $fields) . '`';
		} else {
			$this->select = $fields;
		}
		return $this;
	}

	/**
	 * 设置数据表名称，值
	 * @param bool $name
	 * @param mixed $fields
	 */
	public function table($name, $fields = array())
	{
		$this->table = $name;
		$this->fields = $fields;
		return $this;
	}

	/**
	 * 重置条件
	 * @return $this
	 */
	public function reset()
	{
		$this->sql = '';
		$this->select = '*';
		$this->where = array();
		return $this;
	}

	/**
	 * @param array $data
	 * 拼接key valure 数组为 = 连接的数据
	 */
	public function joinKeyVal($data)
	{
		$arr = array();
		foreach ($data as $k => $v) {
			if ($this->fields && is_array($this->fields) && is_string($k)) {
				if (in_array($k, $this->fields)) {
					if (is_null($v)) {
						array_push($arr, sprintf("`%s`=NULL", $k));
					} else {
						array_push($arr, sprintf("`%s`=%s", $k, $this->quote($v)));
					}
				} else {
					continue;
				}
			} elseif (is_int($k)) {
				array_push($arr, $v);
			} else {
				array_push($arr, sprintf("`%s`=%s", $k, $this->quote($v)));
			}
		}
		return $arr;
	}

	/**
	 * 给字符串,添加'
	 */
	public static function q($v)
	{
		return "'" . str_replace(array("\\", "'"), array('', "\\'"), trim($v, "'")) . "'";
	}

	//当发生错误 STATE[HY000] CODE[2006] MSG[MySQL server has gone away]
	//时重建连接，检测到此错误时，调用方可以重试查询
	public function errorInfo()
	{
		$errInfo = parent::errorInfo();
		if ($errInfo[0] == 'HY000' && $errInfo[1] == 2006) {
			$this->reConnect();
		}
		if ($this->stmt instanceof PDOStatement) { //绑定参数的方式执行sql出错
			$errInfo = $this->stmt->errorInfo();
			$errInfo['param'] = $this->param;
		}
		$errInfo['sql'] = $this->sql;
		return $errInfo;
	}

	public function exec($statement)
	{
		//$this->debug($statement);
		$this->msg = null;
		$ret = parent::exec($statement);
		if ($ret === false) {
			$this->msg = $this->errorInfo();
			//重试
			if ($this->msg[0] == 'HY000' && $this->msg[1] == 2006) {
				$ret = parent::exec($statement);
				if ($ret !== false) {
					$this->msg = null;
				}
			}
		}
		return $ret;
	}

	public function query($statement)
	{
		//$this->debug($statement);
		$this->msg = null;
		$ret = parent::query($statement);
		if ($ret === false) {
			$this->msg = $this->errorInfo();
			//重试
			if ($this->msg[0] == 'HY000' && $this->msg[1] == 2006) {
				$ret = parent::query($statement);
				if ($ret !== false) {
					$this->msg = null;
				}
			}
		}
		return $ret;
	}


	/**
	 * 执行一条SQL语句并返回影响的行数，执行成功可能也返回0，执行失败则返回false
	 * @param $sql
	 * @param array $param
	 * @return bool|int
	 */
	public function execute($sql, $param = array())
	{
		$this->msg = null;
		$stmt = $this->prepareStatement($sql, $param);
		if ($stmt->execute()) {
			return intval($stmt->rowCount());
		} else {
			$this->msg = $this->errorInfo();
			//重试
			if ($this->msg[0] == 'HY000' && $this->msg[1] == 2006) {
				$stmt = $this->prepareStatement($sql, $param);
				if ($stmt->execute()) {
					$this->msg = null;
					return intval($stmt->rowCount());
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	/**
	 * 返回一个PDOStatement实例
	 * @param $sql string，SQL语句，可以带占位符
	 * @param array $param
	 * @return bool|PDOStatement
	 */
	private function prepareStatement($sql, $param = array())
	{
		try {
			$this->msg = null;
			$this->sql = $sql;
			$this->param = $param;
			$stmt = $this->prepare($sql);
			foreach ($param as $k => $v) {
				if (is_string($k) && substr($k, 0, 5) == ':INT_') {
					$stmt->bindValue($k, $v, PDO::PARAM_INT);
				} elseif (is_int($k)) {
					$stmt->bindValue($k + 1, $v, PDO::PARAM_STR);
				} else {
					$stmt->bindValue($k, $v, PDO::PARAM_STR);
				}
			}
			$this->stmt = $stmt;
			return $stmt;
		} catch (\Exception $e) {
			$this->msg = $this->errorInfo();
			//重试
			if ($this->msg[0] == 'HY000' && $this->msg[1] == 2006) {
				try {
					$stmt = $this->prepare($sql);
					foreach ($param as $k => $v) {
						if (is_string($k) && substr($k, 0, 5) == ':INT_') {
							$stmt->bindValue($k, $v, PDO::PARAM_INT);
						} elseif (is_int($k)) {
							$stmt->bindValue($k + 1, $v, PDO::PARAM_STR);
						} else {
							$stmt->bindValue($k, $v, PDO::PARAM_STR);
						}
					}
					$this->stmt = $stmt;
					$this->msg = null;
					return $stmt;
				} catch (\Exception $e) {
					return false;
				}
			} else {
				return false;
			}
		}
	}
}