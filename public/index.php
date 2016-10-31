<?php
/**
 * 1、入口文件
 * 2、定义常量
 * 3、引入函数库
 * 4、自动加载
 * 5、启动框架
 * 6、路由解析
 * 7、加载控制器
 * 8、返回结果
 */

define('DEBUG', 0); //调试模式
define('ROOT_PATH', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
require __DIR__ . '/../vendor/autoload.php';
\Cy\CyPHP::run();