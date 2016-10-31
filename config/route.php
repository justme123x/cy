<?php
/**
 * app路由映射
 * /
 * /index
 * /news
 * /gg
 */
return [
	//访客可访问
	'/' => ['route' => 'index@index', 'as' => 'home.index', 'nameSpace' => 'Home', 'callBack' => 'guest'],
	'/news' => ['route' => 'index@news', 'as' => 'home.news', 'nameSpace' => 'Home', 'callBack' => 'guest',],

	//登录用户访问
	'/money' => ['route' => 'index@index', 'as' => 'home.index', 'nameSpace' => 'Home', 'callBack' => 'identity'],

	//RBAC权限验证
	'/userList' => ['route' => 'index@index', 'as' => 'home.index', 'nameSpace' => 'Home', 'callBack' => 'role'],
	'/index/cc' => ['route' => 'index@cc', 'as' => 'home.index', 'nameSpace' => 'Home', 'callBack' => 'role'],
];