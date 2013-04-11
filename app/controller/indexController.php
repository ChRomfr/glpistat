<?php

class indexController extends Controller{
	
	public function indexAction(){
		$Stat = $this->load_controller('statsController');
		return $Stat->indexAction();
	}
	
}
