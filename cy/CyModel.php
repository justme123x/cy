<?php

namespace Cy;

class CyModel
{
//	private function __construct() { }
//
//	private function __clone() { }
//
//	public static function instance($table = '')
//	{
//		return new self();
//	}

	private $pdo = null;

	public function __construct()
	{
		$this->pdo = \Cy\CyDB::getDb();
	}
}