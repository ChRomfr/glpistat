<?php

class lieuManager extends BaseModel{
	
	public function getAll(){
		return	$this->db->select('id,name')->from('glpi_locations')->where(array('level =' => 1))->order('name')->get();
	}

}