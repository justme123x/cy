<?php
namespace App\Home\Controllers;

use App\CyController;

class IndexController extends CyController
{
	public function Index()
	{
		dump([]);
	}

	public function News()
	{
		dump($this->params('name'));
	}
}