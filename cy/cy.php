<?php
namespace Cy;

use Cy\lib\route;

class cy
{
	private static $app = [];

	public static function run()
	{
		static::init();
		$class = '\App\\' . static::$app['REQUEST_NAMESPACE'] . '\Controllers\\' . static::$app['REQUEST_CONTROLLER'] . 'Controller';
		$action = static::$app['REQUEST_ACTION'];
		dump(static::$app);
		$app = new $class();
		if (!method_exists($app, $action)) {
			throw new \Exception($class . '未定义' . $action . '方法');
		}
		$app->$action();
	}

	private static function init()
	{
		static::initErrors();
		static::$app = route::init();
	}

	private static function initErrors()
	{
		if (defined('DEBUG') && DEBUG == 1) {
			ini_set('display_errors', true);
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', false);
			error_reporting(0);
		}
	}
}