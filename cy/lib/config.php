<?php
namespace Cy\lib;

class config
{
	private static $config = []; //配置数组

	/**
	 * 获取一个配置项
	 * @param $fileName    配置文件名称
	 * @param $key        配置项名称
	 * @return mixed      配置项值
	 * @throws \Exception
	 */
	public static function getConfig($fileName, $key)
	{
		if (!static::loadConfig($fileName)) {
			throw new \Exception('配置文件不存在' . $fileName . '-->' . $key);
		}
		if (isset(static::$config[$fileName][$key]))
			return static::$config[$fileName][$key];
		return false;
	}

	/**
	 * 加载配置文件
	 * @param $fileName    配置文件名称
	 * @return bool
	 */
	public static function loadConfig($fileName)
	{
		if (isset(static::$config[$fileName]))
			return static::$config[$fileName];
		$file = CONFIG_PATH . $fileName . '.php';
		if (!is_file($file))
			throw new \Exception('配置文件不存在' . $fileName);
		static::$config[$fileName] = include $file;
		unset($data, $file);
		return static::$config[$fileName];
	}
}