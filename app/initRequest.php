<?php
namespace App;

class initRequest
{
	public static function guest()
	{
		dump('init request success');
		return true;
	}

	public static function identity()
	{
		return true;
	}

	public static function role()
	{
		return true;
	}
}