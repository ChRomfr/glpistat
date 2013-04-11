<?php

class BaseutilisateurController extends Controller{
        
    public function indexAction(){
    	if( $_SESSION['utilisateur']['id'] == 'Visiteur')
    		return $this->registerAction();
    	else
    		return $this->profilAction();
    }
    
    /**
	*	Affichage et traitement du formulaire d enregistrement utilisateur
	*	@return mixed code html
	*/
	public function registerAction(){
		
		global $config;
	
		if( $this->registry->HTTPRequest->postExists('user') ){
			// Traitement du formulaire
			$this->load_model('utilisateur', 'base_app');
			$user = new BaseUtilisateur($this->registry->HTTPRequest->postData('user') );
			$this->load_manager('utilisateur', 'base_app');
			
			if( $user->isValid() == false )
				goto print_form;
				
			if( $user->validPassword($_POST['user']['password2']) == false ){
				$this->registry->smarty->assign('print_error', $this->lang['Mot_de_passe_invalide']);
				goto print_form;
			}	
			
			$user->cryptPassword();
			$user->save($this->manager->utilisateur);
			
			return $this->redirect(getLink('index'), 3, $this->lang['Compte_cree']);
		}
		
		print_form:
		$this->registry->addJS('jquery-1.6.2.min.js');
		$this->getFormValidatorJs();
		return $this->registry->smarty->fetch(BASE_APP_PATH . 'view' . DS . 'utilisateur' . DS . 'register.tpl');
		
	}
    
    /**
     * Requete qui verifie si l identifiant existe deja dans la base utilisateur
     * 
     * 
     */
    public function check_identifiantAction($identifiant){
		$this->load_manager('utilisateur', 'base_app');

		$result = $this->manager->utilisateur->existIdentifiant(trim(htmlentities($identifiant)));

		if( $result > 0 )
			return 'alreadyUse';
		else
			return '';
	}
	
	public function check_emailAction($email){
		$this->load_manager('utilisateur', 'base_app');
		
		$result = $this->manager->utilisateur->existEmail(trim(htmlentities($email)));

		if( $result > 0 )
			return 'alreadyUse';
		else
			return '';
	}
	
	
        
}