<?php
namespace Cy\lib;


class route
{
	private static $route = [];
	private static $uri = '/';

	public static function init()
	{
		return static::getRoute();
	}

	public static function getRoute()
	{
		static::$route = config::loadConfig('route');
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		if (isset(static::$route[$uri])) {
			return static::initRoute($uri);
		}

		dump($uri);
		foreach (static::$route as $route => $routeParams) {
			$route_tmp = preg_quote($route, '/');
			dump('/^[' . $route_tmp . ']/i');
			if ($result = preg_match('/^[' . $route_tmp . ']/i', $uri)) {
				return static::initRoute($route);
			}
		}

		throw new \Exception('页面不存在');
	}

	private static function initRoute($routeKey)
	{
		$route = explode('@', static::$route[$routeKey]['route']);
		$params = explode('/', trim(static::$uri, '/'));

		return [
			'REQUEST_NAMESPACE' => ucfirst(static::$route[$routeKey]['nameSpace']),
			'REQUEST_CALLBACK' => static::$route[$routeKey]['callBack'],
			'REQUEST_CONTROLLER' => ucfirst($route[0]),
			'REQUEST_ACTION' => ucfirst($route[1]),
			'REQUEST_PARAMS' => $params
		];
	}
}