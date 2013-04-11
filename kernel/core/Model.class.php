<?php if(!defined('IN_VA')) exit;

abstract class BaseModel {
	
	protected $db;
	
	protected $cache;
	
	public function  __construct() {
		$this->db = Db::getInstance();
	}
}