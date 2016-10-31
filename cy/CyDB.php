<?php
namespace Cy;
class CyDB
{
	private static $pdo;

	private function __construct() { }

	private function __clone() { }

	public static function getDb()
	{
		if (static::$pdo instanceof \PDO) return static::$pdo;

		try {
			return $pdo = new \PDO(
				\Cy\CyConfig::getConfig('DataBase', 'DSN'),
				\Cy\CyConfig::getConfig('DataBase', 'USER'),
				\Cy\CyConfig::getConfig('DataBase', 'PASSWD'),
				\Cy\CyConfig::getConfig('DataBase', 'OPTIONS')
			);
		} catch (\PDOException $e) {
			echo '数据库连接失败：' . $e->getMessage();
		}
	}


}