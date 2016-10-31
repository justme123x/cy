<?php

namespace Cy;

class CyModel
{
	private $pdo = null;
	private $where = [];
	private $group = [];

	private function __construct()
	{
		$this->pdo = \Cy\CyDB::getDb();
	}

	private function sqlAddCond ($cond) {
		$s = '';
		if(!empty($cond)) {
			$s = ' WHERE ';
			foreach($cond as $k=>$v) {
				if(!is_array($v)) {
					$v = addslashes($v);
					$s .= "$k = '$v' AND ";
				} else {
					foreach($v as $k1=>$v1) {
						if($k1 == 'IN'){
							is_string($v1) AND $v1 = explode(',', $v1);
							$s .= "$k IN (".implode(',', $v1).") AND ";
						} else {
							$v1 = addslashes($v1);
							$k1 == 'LIKE' AND $v1 = "%$v1%";
							$s .= "$k $k1 '$v1' AND ";
						}
					}
				}
			}
			$s = substr($s, 0, -4);
		}
		return $s;
	}
}