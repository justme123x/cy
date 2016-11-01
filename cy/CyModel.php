<?php

namespace Cy;

class CyModel
{
	private static $db = null;

	public $table;   //当前操作的数据表名字

	public function __construct()
	{
		if (is_null(self::$db)) {
			try {
				self::$db = new CyDB();
			} catch (\PDOException $e) {
				throw  new \Exception('数据连接失败.' . $e->getMessage());
			}
		}
		$this->reset();
		$this->table($this->table);
	}

	/**
	 * 设置数据表名称，值
	 * @param bool $name
	 * @param mixed $fields
	 */
	public function table($name, $fields = array())
	{
		self::$db->table($name, $fields);
		return $this;
	}

	/**
	 * 插入数据
	 * @param array $data
	 */
	public function insert($data = array())
	{
		return self::$db->insert($data);
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
		return self::$db->insertDuplicate($insertData, $updateData);
	}

	/**
	 * 计算有多少条记录
	 */
	public function count($strCountSql = 'count(*)')
	{
		return self::$db->count($strCountSql);
	}

	/**
	 * SQL语句查询单行数据
	 * @param $sql
	 * @param int $type
	 * @param int $returnType 1 默认（sql错误时返回空数组，没查到数据时返回false） ，2 （sql错误时返回 false，没查到数据时返回空数组）
	 * @return array|bool|mixed
	 */
	public function row($sql, $type = PDO::FETCH_ASSOC, $returnType = 1)
	{
		return self::$db->row($sql, $type, $returnType);
	}

	/**
	 * SQL语句查询多行数据
	 * @param $sql
	 * @param int $start
	 * @param int $size
	 * @param string $orderBy
	 * @param string $groupBy
	 * @param int $type
	 * @return array
	 */
	public function rows($sql, $start = 0, $size = 0, $orderBy = '', $groupBy = '', $type = \PDO::FETCH_ASSOC)
	{
		return self::$db->rows($sql, $start, $size, $orderBy, $groupBy, $type);
	}

	/**
	 * 更新数据
	 * @param array $data
	 * 返回受影响的行数/出错返回false
	 */
	public function update($data = array())
	{
		return self::$db->update($data);
	}

	/**
	 * 根据查询条件返回一条记录
	 * @param int $returnType 参见 $this->row() 注释
	 * @return array|mixed
	 */
	public function find($returnType = 1)
	{
		return self::$db->find($returnType);
	}

	/**
	 * 根据查询条件返回多条记录
	 */
	public function findAll($offset = 0, $limit = 0, $orderBy = '', $groupBy = '')
	{
		return self::$db->findAll($offset, $limit, $orderBy, $groupBy);
	}

	/**
	 * 删除数据
	 */
	public function delete()
	{
		return self::$db->delete();
	}

	/**
	 * 设置各种查询条件
	 * @param mixed $input
	 */
	public function where(array $input)
	{
		self::$db->where($input);
		return $this;
	}


	public function select($fields)
	{
		self::$db->select($fields);
		return $this;
	}


	/**
	 * 重置条件
	 * @return $this
	 */
	public function reset()
	{
		self::$db->reset();
		return $this;
	}

	/**
	 * 获取最后一次执行的SQL语句
	 * @return string
	 */
	public function getLastSql()
	{
		return self::$db->sql;
	}

}