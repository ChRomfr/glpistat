<?php

class Session {

	private $registry;

	public $session;
	
	private $db;
	
	public $user_id;
	
	public $_session;
	
	public function __construct($registry = null){
		global $db;
		if( !is_null($registry) ) $this->registry = $registry;
		$this->session = $_SESSION;
		$this->db = $db;
	}
	
	public function check(){

		if( !isset($this->session['session_id']) ) return false;
		if( !isset($this->session['utilisateur']) ) return false;
		if( !is_array($this->session['utilisateur']) ) return false;
		if( !isset($this->session['token']) )  return false;
		if( $this->db->count(PREFIX . 'sessions', array('session_id = ' => $this->session['session_id']), 'session_id') != 1) return false;
		
		//if(TRACK_USER) $this->updateSessionInDb();
		$this->user_id = $this->session['utilisateur']['id'];
		$this->update();
		return true;
	}
	
	public function update(){
		$this->db->update(PREFIX . 'sessions', array('session_id' => $this->session['session_id'], 'last_used' => time()) , array('session_id =' => $this->session['session_id']) );
	}
	
	public function clearSession($delay = 86400 ){
		$delay = time() - $delay;
		$this->db->delete(PREFIX . 'sessions', null, array('create_on <' => $delay) );
	}
	
	public function checkTime(){
		if( $this->session['creat_on'] + 600 < time() ){
			session_regenerate_id();
			$this->session['creat_on'] = time();
		}
	}
	
	public function create($user_id = 'Visiteur'){
		
		if( isset($_SESSION['utilisateur']) ) unset($_SESSION['utilisateur']);

		if( $user_id == 'Visiteur' ){
			$this->createVisiteurSession();
			$this->storeInDb();
		}
		
		$this->user_id = $user_id;
		$_SESSION = $this->session;	
	}
	
	public function creatSession($user){

		foreach($user as $k => $v){
			if( $k != 'password' )
				$this->session['utilisateur'][$k] = $v;
		}
		
		$this->session['session_id'] = getUniqueId();
		$this->session['token'] = getUniqueId();
		$this->session['creat_on'] = time();
		$this->session['ip'] = $_SERVER['REMOTE_ADDR']; 
		$this->storeInDb();
		$_SESSION = $this->session;
	}
	
	public function updateSession($user){
		
		foreach($user as $k => $v){
			if( $k != 'password' )
				$this->session['utilisateur'][$k] = $v;
		}
		
		$this->session['session_id'] = $_SESSION['session_id'];
		$this->session['token'] = $_SESSION['token'];
		$this->session['creat_on'] = time();
		$this->session['ip'] = $_SERVER['REMOTE_ADDR']; 
		//$this->storeInDb();
		$_SESSION = $this->session;
		
	}
	
	public function checkToken(){
		$token = $_REQUEST['token'];
		if( $this->session['token'] == $token) return true;
		
		return false;
	}
	
	public function destroy(){
		$this->deleteBySessionId();
		session_destroy();
	}
	
    /**
     * Verifie si un utilisateur est identifié.
     * @return bool true:identifié false:visiteur
     * */
	public function isIdentified(){
		if( $this->session['utilisateur']['id'] != 'Visiteur') return true;
		
		return false;
	}
	
	public function isAdmin(){
		if( isset($this->session['utilisateur']['isAdmin']) && $this->session['utilisateur']['isAdmin'] == 1) return true;
		else return false;
	}
    
    public function userUpdate(){
        $this->session['utilisateur'] = getUserInfo( $this->session['utilisateur']['id'] );
        $this->session['utilisateur']['niveau'] = $this->session['utilisateur']['level'];
        $_SESSION['utilisateur'] = $this->session['utilisateur'];
    }
	
	private function deleteBySessionId(){
		$this->db->exec( 'DELETE FROM '. PREFIX .'sessions WHERE session_id = "'. $this->session['session_id'] .'" ');
	}
	
	private function deleteByUserId(){
		$this->db->exec( 'DELETE FROM '. PREFIX .'sessions WHERE user_id = "'. $this->session['utilisateur']['id'] .'" ');
	}
	
	private function createVisiteurSession(){
		$this->session['utilisateur'] = array();
		$this->session['session_id'] = getUniqueId();
		$this->session['token'] = getUniqueId();
		$this->session['creat_on'] = time();
		$this->session['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->session['utilisateur']['actif'] = 1;
		$this->session['utilisateur']['id'] = 'Visiteur';
		$this->session['utilisateur']['identifiant'] = 'Visiteur';
		$this->session['utilisateur']['isAdmin'] = 0;
	}
	
	private function createUserSession($id){
        $result = $this->db->get_one(PREFIX . 'utilisateur', array('id = ' => $id));
		$this->session['utilisateur'] = $result;
		unset( $this->session['utilisateur']['password'] );
		$this->session['session_id'] = getUniqueId();
		$this->session['token'] = getUniqueId();
		$this->session['creat_on'] = time();
		$this->session['ip'] = $_SERVER['REMOTE_ADDR'];
		
		if( $this->session['utilisateur']['type'] == 'pro' ){
			$this->session['utilisateur'] = array_merge($this->session['utilisateur'], $this->db->get_one(PREFIX . 'utilisateur_pro', array('id =' => $id)));
		}elseif( $this->session['utilisateur']['type'] == 'par' ){
			$this->session['utilisateur'] = array_merge($this->session['utilisateur'], $this->db->get_one(PREFIX . 'utilisateur_par', array('id =' => $id)));
		}
	}
	
	private function updateInDb(){
		return $this->db->exec('UPDATE '. PREFIX . 'sessions SET WHERE session_id = ' . $this->session['session_id'].'');
	}
	
	private function storeInDb(){
		return $this->db->exec( 'INSERT INTO '. PREFIX . 'sessions (session_id, user_id, ip, create_on, last_used) VALUES ("'. $this->session['session_id'] .'", "'. $this->session['utilisateur']['id'] .'", "'. $this->session['ip'] .'", "'. $this->session['creat_on'] .'", "'. time() .'" ) ');
	}	
}