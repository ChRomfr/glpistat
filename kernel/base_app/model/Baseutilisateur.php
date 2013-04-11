<?php

class Baseutilisateur extends Record{
		
	public 	$id,
			$identifiant,
			$email,
			$password,
			$actif;
	public	$register_on;
	public	$last_connexion;
	
	public function setActif( $str ){
		$this->actif = $str;
	}
	
	public function getEmail(){
		return $this->email;
	}
			
	public function isValid(){
		
		if( empty($this->identifiant) || empty($this->email) ){
			return false;
		}
		
		return true;
	}
	
	public function validPassword($confirmation){
		
		if( empty($this->password) ){
			return false;
		}
		
		if( strlen($this->password) < 6){
			return false;
		}
		
		if( $this->password != $confirmation){
			return false;
		}
		
		return true;		
	}
	
	public function cryptPassword(){
		$this->password = sha1( sha1(strtolower($this->identifiant)) . $this->password );
	}
	
	public function checkLogin($manager){
		
		if( empty($this->identifiant) || empty($this->password) ){
			return false;
		}
		
		$this->cryptPassword();
		$data = $manager->getByIdentifiantAndPassword($this);
		
		if( empty($data) )
			return false;

		$this->hydrate($data);
		
		return true;
	}
	
	public function save($manager){
		
		if( empty($this->id) ){
			// Nouvel utilisateur
			$this->register_on = time();
			$this->actif = 1;
			$this->last_connexion = 0;
			$this->id = $manager->save($this);			
		}else{
			$manager->save($this);
		}
		
		
	}
}