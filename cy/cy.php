<?php
namespace Cy;


class cy
{
	private static $request = []; //本次请求参数集合
	private static $app = null; //本次请求app 对象

	public static function run()
	{
		//初始化框架
		static::init();
	}

	/**
	 * 各类初始化集合
	 */
	private static function init()
	{
		static::initErrors();
		static::$request = \Cy\lib\route::init();
		static::initRequest();
		static::startApp();
		return;
	}

	/**
	 * 访问启动框架 初始访问权限
	 */
	private static function initRequest()
	{
		if (isset(static::$request['REQUEST_INIT'])) {
			$initFnc = static::$request['REQUEST_INIT'];
			return \App\initRequest::$initFnc();
		}
		return;
	}

	/**
	 * 框架DEBUG初始化
	 */
	private static function initErrors()
	{
		if (defined('DEBUG') && DEBUG == 1) {
			ini_set('display_errors', true);
			error_reporting(E_ALL);
			restore_error_handler();
			return;
		} else {
			ini_set('display_errors', false);
			error_reporting(0);
			return;
		}
	}

	/**
	 * 启动请求app
	 */
	private static function startApp()
	{
		//拼写命名空间
		$class = '\App\\' . static::$request['REQUEST_NAMESPACE'] . '\Controllers\\' . static::$request['REQUEST_CONTROLLER'] . 'Controller';
		//方法名
		$action = static::$request['REQUEST_ACTION'];
		//实例化控制器 并传递本次路由请求参数
		static::$app = new $class(static::$request['REQUEST_PARAMS']);
		//调用方法
		static::$app->$action();
		return;
	}
}