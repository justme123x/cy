<?php
namespace \Cy\lib;

class factory
{
	private static $objectList = [];

	private function __construct() { }

	private function __clone() { }

	/**
	 * 获取一个类的单例对象
	 * @param $class
	 * @param array $params
	 * @return mixed
	 */
	public static function getObject($class, $params = [])
	{
		if (isset(static::$objectList[$class])) return static::$objectList[$class];
		try {
			return static::$objectList[$class] = new $class($params);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}