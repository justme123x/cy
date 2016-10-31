<?php
namespace App;
class CyController
{
	private $_params = [];

	public function __construct($routeParams)
	{
		$this->_params = $routeParams;
	}

	public function params($key, $defaultVal = false)
	{
		if (is_int($key) && isset($this->_params[$key])) return $this->_params[$key];
		if (isset($_REQUEST[$key])) return $_REQUEST[$key];
		return $defaultVal;
	}
}

