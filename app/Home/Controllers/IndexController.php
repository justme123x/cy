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
		dump(\Cy\CyDB::getDb());
		dump(\Cy\CyDB::getDb());
		dump(\Cy\CyDB::getDb());
		dump(\Cy\CyDB::getDb());
	}
}