<?php

class Router{
 /*
 * @the registry
 */
 private $registry;

 /*
 * @the controller path
 */
 private $path;

 public $args = array();

 public $file;

 public $controller;

 public $action; 

 function __construct($registry) {
        $this->registry = $registry;
		$this->getRoute();
 }
 
 function getRoute(){
	
	if( !isset($_SERVER['REDIRECT_QUERY_STRING']) )
		$route = str_replace($_SERVER['SCRIPT_NAME'] .'/','', $_SERVER['REQUEST_URI']);
	else
		$route = $_SERVER['REDIRECT_QUERY_STRING'];
		
	
	$route = explode('?', $route);
	$route = explode('&', $route[0]);
	$route = explode('/',$route[0]);
	//var_dump($route);
	if( !empty($route[0]) )
		$this->controller = $route[0];
	else
		$this->controller = 'index';
		
	if( !empty($route[1]) )
		$this->action = $route[1];
	else
		$this->action = 'index';
	
	if( !empty($route[2]) )
		$this->args = $route[2];
		
	
 }
 
function setPath($path) {
	
	if( !is_array($path) ):
		/*** check if path i sa directory ***/
		if (is_dir($path) == false)
		{
			throw new Exception ('Invalid controller path: `' . $path . '`');
		}
		/*** set the path ***/
		$this->path = $path;
	else:
		foreach($path as $row):
			if( is_dir($row) && is_file($row . $this->controller . 'Controller.php') ):
				$this->path = $row;
			endif;
		endforeach;
	endif;
}
 
 public function loader(){
	$this->getController();
	
	$this->action = $this->action.'Action';
	$this->controller = $this->controller . 'Controller';
	
	if( !method_exists($this->controller, $this->action) || !is_callable(array($this->controller, $this->action)) ){
		$this->action = 'indexAction'; 
	}
	$action = $this->action;

	$class = new $this->controller($this->registry);
	
	return $class->$action($this->args);
}
 
 public function getController(){
	
	if( is_file($this->path . $this->controller . 'Controller.php') ){
		require_once $this->path . DS . $this->controller . 'Controller.php';
	}else{
		require_once $this->path . DS . 'indexController.php';
	}
	/*
	if( isset($_GET['c']) && is_file($this->path . 'controller' . DS . $_GET['c'] . '.php') ){
		require $this->path . 'controller' . DS . $_GET['c'] . '.php';
		
		if( $_GET['c'] == 'annonce' || $_GET['c'] == 'utilisateur' || $_GET['c'] == 'enchere' || $_GET['c'] == 'newsletter'):
			$this->controller = $_GET['c'].'Controller';
		else:
			$this->controller = $_GET['c'];
		endif;
		
	}else{
		require $this->path . 'controller' . DS . DEFAULT_CONTROLLER . '.php';
		$this->controller = DEFAULT_CONTROLLER;
	}	
	*/
 }
 
}