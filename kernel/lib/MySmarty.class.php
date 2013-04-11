<?php
class MySmarty extends Smarty{
	
	private $registry;
	
	protected	$_cache_tpl_db = 0;
	
	protected	$_use_tpl_perso = 0;
	
	protected	$_tpls;
	
	public		$tpls_used = array();
	
	public function __construct($registry){
		
		$this->registry = $registry;
		parent::__construct();
		$this->debugging = false;
		$this->template_dir = VIEW_PATH;
		$this->compile_dir = CACHE_PATH; // Repertoire du cache compilé
		$this->cache_dir = CACHE_PATH;   // Repertoire du cache
		$this->cache_lifetime = 3600; // Duree de vie du cache
		$this->allow_php_tag=true; // Permet l utilisatuin de php dans le template
		
		if( $this->_use_tpl_perso == 1 && $this->_cache_tpl_db == 1 ):
		
			if( !$this->_tpls = $this->registry->cache->get("templates_in_db") ):	
			
				// Recuperation des templates en base
				$tmp = $this->registry->db->get(PREFIX . 'view_template', array('active =' => 1) );
				
				// On boucle sur le resultat pour le mettre dans la viariable
				foreach( $tmp as $row ):
					$this->_tpls[''. $row['real_dir'] .''] = $row['tpl'];
				endforeach;
				
				// On sauvegarde le resultat en cache
				$this->registry->cache->save( serialize($this->_tpls) );
			else:
				$this->_tpls = unserialize($this->_tpls);
			endif;
			
		endif;
		
	}
	
	/**
	*	Surcharge de la fonction FETCH pour verifier si le TPL existe en base de donnees
	*
	*/
	public function fetch($tpl_dir, $cache_id = null, $compile_id = null, $parent = null, $display = false){

		if( $this->_use_tpl_perso == 1 ):
		
			$file_search = str_replace(ROOT_PATH,'',$tpl_dir);
			$file_search = str_replace(DS,'_',$file_search);
			
			if( IN_PRODUCTION == FALSE):
				$this->tpls_used[$tpl_dir] = $file_search;
			endif;
				
			if( $this->_cache_tpl_db == 1 && isset($this->_tpls[$file_search]) ):
				return parent::fetch('string:' . $this->_tpls[$file_search], $cache_id, $compile_id, $parent, $display);
			elseif( !isset($this->_tpls[$file_search]) &&  $this->_cache_tpl_db == 1):
				goto normal_fetch;
			elseif( $this->_cache_tpl_db == 0 ):
				$_tpl = $this->registry->db->get_one(PREFIX . 'view_template', array('real_dir =' => $file_search, 'active =' => 1) );
			endif;
			
			
			if( !empty($_tpl) && is_array($_tpl) ):
				return parent::fetch('string:' . $_tpl['tpl'], $cache_id, $compile_id, $parent, $display);
			endif;
		endif;
		
		normal_fetch:
		return parent::fetch($tpl_dir, $cache_id, $compile_id, $parent, $display);
		
	}
}
?>