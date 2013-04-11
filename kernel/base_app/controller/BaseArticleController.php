<?php
/**
*	SHARKPHP VA
*	CMS FOR VIRTUAL AIRLINE
*	@author ChRom
*	@web http://va.sharkphp.com
*/

if( !defined('IN_VA')) exit;

class Basearticlecontroller extends Controller{
	
	public function indexAction(){		
		
		require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Tree.class.php';
		$Tree = new Tree($this->app->db, PREFIX . 'article_categorie');
		
		$this->load_manager('article', 'base_app');
		
		if( $this->app->HTTPRequest->getExists('cid') ): 
			$categorie_id = $this->app->HTTPRequest->getData('cid');
			$Categorie = $this->app->db->get_one(PREFIX . 'article_categorie', array('id =' => $categorie_id));
			$Categories = $this->app->db->get(PREFIX . 'article_categorie', array('parent_id =' => $categorie_id), 'name');
			$Parents = $Tree->getParent($Categorie['lft'], $Categorie['rght']);
			
			$this->app->smarty->assign(array(
				'Parents'		=>	$Parents,
				'Categorie'		=>	$Categorie,
			));
			
		else:
			$categorie_id = '';	
			$Categories = $this->app->db->get(PREFIX . 'article_categorie', array('parent_id =' => 0), 'name');
		endif;					
		
		$Articles = $this->manager->article->getAll( array('categorie_id =' => $categorie_id) );
		
		
		$this->app->smarty->assign(array(
			'Articles'		=>	$Articles,
			'Categories'	=>	$Categories,
			'ctitre'		=>	$this->lang['Article'],
		));
		
		return $this->registry->smarty->fetch(BASE_APP_PATH . 'view' . DS . 'article' . DS . 'index.tpl');
	}
	
	public function readAction($article_id){
		
		$this->load_manager('article', 'base_app');
		
		$Article = $this->manager->article->getById( $article_id );
		
		// Recuperation de la branche des catégories
		if( !empty($Article['categorie_id']) ):
			require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Tree.class.php';
			$Tree = new Tree($this->app->db, PREFIX . 'article_categorie');
			$Categories = $Tree->getParent($Article['lft'], $Article['rght']);
			$this->app->smarty->assign('Parents', $Categories);
		endif;
		
		if( empty($Article) ) return $this->redirect( getLink('index/index'), 0, '');
		
		$this->app->smarty->assign(array(
			'Article'	=>	$Article,
			'ctitre'	=>	$Article['title'],
		));
		
		return $this->registry->smarty->fetch(BASE_APP_PATH . 'view' . DS . 'article' . DS . 'read.tpl');
	}
	
}