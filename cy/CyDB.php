<?php
namespace Cy;
class CyDB
{
	private static $pdo;

	private function __construct() { }

	private function __clone() { }

	public static function getDb()
	{
		$dsn = '';
		$username = '';
		$passwd = '';
		$options = [];
		if (static::$pdo instanceof \PDO) return static::$pdo;

		try {
			$pdo = new \PDO($dsn, $username, $passwd, $options);
		} catch (\Exception $e) {
			echo '数据库连接失败：' . $e->getMessage();
		}
	}


}