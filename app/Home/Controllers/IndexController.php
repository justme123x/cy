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

		$cond = [];
		$cond['id'] = 1;
		$cond['ymd'] = ['>=' => 20151001, '<=' => 2015111];
		$cond['type'] = ['IN' => [1, 2, 3, 4, 5]];
		$cond['role'] = ['not in' => [1, 2, 3, 4, 5]];
		$cond['name'] = ['like' => '%san%'];

		$data = [];
		$data['id'] = 5;
		$data['name'] = '张三';
		$obj->select(['id','name'])->where($cond)->findAll();

		$obj->reset()->insert($data);

		$obj->reset()->where($cond)->update($data);

		$cond = ['id'=>1];
		$obj->reset()->where($cond)->find();
	}


}