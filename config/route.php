<?php
/**
 * 路由配置
 * 路由后缀网址 请cy/lib/Route.php 配置
 */
return [

	/**
	 * 正则路由匹配
	 * /news-[1-9]+ => news-11.html
	 * /news-cc-[1-9]+ => news-cc-11.html
	 * 不同应用切换
	 * /admin/index =>/admin/index.html
	 * /admin/index-[\d]+ =>/admin/index-root.html
	 * '/news-[1-9]+-[a-z]+' => ['route' => 'index@news', 'as' => 'home.news', 'nameSpace' => 'Home', 'initRequest' => 'guest'],
	 * 	路由地址				=>               控制器@方法        别名                        命名空间                初始化请求
	 */

	//访客可访问
	'/' => ['route' => 'index@index', 'as' => 'home.index', 'nameSpace' => 'Home', 'initRequest' => 'guest'],
	'/news-[1-9]+-[a-z]+' => ['route' => 'index@news', 'as' => 'home.news', 'nameSpace' => 'Home', 'initRequest' => 'guest'],
	'/index' => ['route' => 'index@news', 'as' => 'home.news', 'nameSpace' => 'Home', 'initRequest' => 'guest'],

	//登录用户访问
	'/money' => ['route' => 'index@index', 'as' => 'home.index', 'nameSpace' => 'Home', 'initRequest' => 'identity'],

	//RBAC权限验证
	'/userList' => ['route' => 'user@list', 'as' => 'home.index', 'nameSpace' => 'Home', 'initRequest' => 'role'],
	'/cc' => ['route' => 'index@cc', 'as' => 'home.index', 'nameSpace' => 'Home', 'initRequest' => 'role'],
	'/admin/cc' => ['route' => 'index@cc', 'as' => 'home.index', 'nameSpace' => 'Home', 'initRequest' => 'role'],
];