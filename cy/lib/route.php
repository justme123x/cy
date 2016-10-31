<?php
namespace Cy\lib;


class route
{
	private static $route = [];
	private static $uri = '/';
	private static $endFix = '.html'; //网址后缀

	public static function init()
	{
		return static::getRoute();
	}

	/**
	 * 定位本次请求在/config/route.php中定义的路由
	 * 未匹配路由，抛出异常，后期可做error页面。
	 * @return array
	 * @throws \Exception
	 */
	public static function getRoute()
	{
		static::$route = config::loadConfig('route');
		static::$uri = isset($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI'], '#') : '/';
		static::$uri = str_replace('#', '', static::$uri);
		if (isset(static::$route[static::$uri])) {
			return static::initRoute(static::$uri);
		} else {
			foreach (static::$route as $route => $routeParams) {
				if ($route == '/') continue;
				$requestRoute = $route . '\\' . static::$endFix;
				$regStr = '#^' . $requestRoute . '#i';
				if ($result = preg_match($regStr, static::$uri)) {
					return static::initRoute($route);
				}
			}
		}
		throw new \Exception('页面不存在');
	}

	/**
	 * 为框架初始化命名空间、初始化方法、控制器、方法、参数
	 * @param $routeKey /config/route.php 定义的 路由key
	 * @return array
	 */
	private static function initRoute($routeKey)
	{
		$route = explode('@', static::$route[$routeKey]['route']);
		return [
			'REQUEST_NAMESPACE' => ucfirst(static::$route[$routeKey]['nameSpace']),
			'REQUEST_INIT' => static::$route[$routeKey]['initRequest'],
			'REQUEST_CONTROLLER' => ucfirst($route[0]),
			'REQUEST_ACTION' => ucfirst($route[1]),
			'REQUEST_PARAMS' => static::initParams($routeKey),
		];
	}

	/**
	 * 整理路由里面的参数
	 * a-b-c.html?name=root
	 * @param $routeKey
	 * @return array
	 */
	private static function initParams($routeKey)
	{
		$pattern = '#^/[\d\w]+-|\\' . static::$endFix . '.*#i';
		$uriParamsStr = preg_replace($pattern, '', static::$uri);
		return explode('-', $uriParamsStr);
	}
}