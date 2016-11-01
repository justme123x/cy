<?php
namespace App\Home\Controllers;



class IndexController extends \Cy\CyController
{
	public function Index()
	{
		echo 'hell world';
	}

	public function News()
	{

		$obj = new \App\Model\Test();

//		$cond = [];
//		$cond['id'] = 1;
//		$cond['ymd'] = ['>=' => 20151001, '<=' => 2015111];
//		$cond['type'] = ['IN' => [1, 2, 3, 4, 5]];
//		$cond['role'] = ['not in' => [1, 2, 3, 4, 5]];
//		$cond['name'] = ['like' => '%san%'];
//
//		$data = [];
//		$data['id'] = 5;
//		$data['name'] = '张三';
//		$obj->table('test');
//		dump($obj->select(['id','name'])->where($cond)->findAll());

		$cond = ['id'=>1];
		dump($obj->where($cond)->find());
	}


}