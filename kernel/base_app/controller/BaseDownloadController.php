<?php
/**
*	SHARKPHP VA
*	CMS FOR VIRTUAL AIRLINE
*	@author ChRom
*	@web http://va.sharkphp.com
*/

if( !defined('IN_VA')) exit;

class Basedownloadcontroller extends Controller{
	
	/**
	*	Affiche la liste des telechagements
	*	@param int $cid : Id de la categorie a afficher
	*	@return void
	*/
	public function indexAction(){
		$dl_per_page = 10;
		
		require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Tree.class.php';
		$Tree = new Tree($this->app->db, PREFIX . 'download_categorie');
		
		$this->load_manager('download', 'base_app');
		
		if( $this->app->HTTPRequest->getExists('cid') ): 
			$categorie_id = $this->app->HTTPRequest->getData('cid');
			$Categorie = $this->app->db->get_one(PREFIX . 'download_categorie', array('id =' => $categorie_id));
			$Categories = $this->app->db->get(PREFIX . 'download_categorie', array('parent_id =' => $categorie_id), 'name');
			$Parents = $Tree->getParent($Categorie['lft'], $Categorie['rght']);
			
			$this->app->smarty->assign(array(
				'Parents'		=>	$Parents,
				'Categorie'		=>	$Categorie,
			));
			
		else:
			$categorie_id = 0;	
			$Categories = $this->app->db->get(PREFIX . 'download_categorie', array('parent_id =' => 0), 'name');
		endif;					
		

		$Downloads	= $this->manager->download->getAll( $dl_per_page, getOffset($dl_per_page), $categorie_id );
		$NbDownload	= $this->manager->download->count($categorie_id);
		
		$this->app->smarty->assign(array(
			'Downloads'		=>	$Downloads,
			'Categories'	=>	$Categories,
			'ctitre'		=>	$this->lang['Telechargement'],
			'Pagination'	=>	$NbDownload > $dl_per_page ? getPagination( array('perPage'=>$dl_per_page, 'fileName'=>getLink('download/index?cid='.$categorie_id) .'&page=%d', 'totalItems'=>$NbDownload) ) : '',
		));		
				
		return $this->registry->smarty->fetch(BASE_APP_PATH . 'view' . DS . 'download' . DS . 'index.tpl');
	}
	
}