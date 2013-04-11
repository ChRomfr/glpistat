<?php
abstract class Record implements ArrayAccess{

	//public $error;
	
	public function __construct(array $donnees = array()){
		if (!empty($donnees)){
	    	$this->hydrate($donnees);
		}
	}	
	
	public function hydrate(array $donnees){
		foreach($donnees as $k => $v){
			if( property_exists($this, $k) ){
				$this->$k = $v;
			}
		}
	}
	

	public function offsetGet($offset) {
		if( isset($this->$offset) )
			return $this->$offset;
    }
	
	public function offsetSet($var, $value)
	{
	$method = 'set'.ucfirst($var);
	
	if (isset($this->$var) && is_callable(array($this, $method)))
	{
	    $this->$method($value);
	}
	}
	
	public function offsetExists($var)
	{
	return isset($this->$var) && is_callable(array($this, $var));
	}
	
	public function offsetUnset($var)
	{
	throw new Exception('Impossible de supprimer une quelconque valeur');
	}
	
	}
?>