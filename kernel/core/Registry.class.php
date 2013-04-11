<?php

Class Registry {

 /*
 * @the vars array
 * @access private
 */
 private $vars = array();
 
 public static $css = array();
 public static $js = array();
 public $config = array();

 /**
 *
 * @set undefined vars
 *
 * @param string $index
 *
 * @param mixed $value
 *
 * @return void
 *
 */
 public function __set($index, $value)
 {
	$this->vars[$index] = $value;
 }

 /**
 *
 * @get variables
 *
 * @param mixed $index
 *
 * @return mixed
 *
 */
 public function __get($index)
 {
	return $this->vars[$index];
 }

 public function addJS($js){
	self::$js[] = $js;
 }
 
 public function addCSS($css){
	self::$css[] = $css;
 }

	/**
	*	Genere le code HTML en fonction d une position
	*	@param string $position left|right|top|foot
	*	@return string $html Code html a afficher
	*/ 
	public function getBlok($position){
		$bloks = $this->db->get(PREFIX . 'blok', array('position =' => $position, 'visible =' => 1), 'ordre ASC');
		
		$html = NULL;
		
		foreach($bloks as $blok){
		
			if( $blok['only_index'] == 1 && $this->router->controller != 'indexController')
				continue;
			
			if( $blok['type'] == 'HTML'){
				require_once APP_PATH . 'blok' . DS . 'blokHTML.php';
				$html .= blokHTML($blok, $this);
			}elseif($blok['type'] == 'MENU'){
				// Construction du menu
				require_once APP_PATH . 'blok' . DS . 'blokMENU.php';
				$html .= blokMENU($blok['contenu'], $this);
			}elseif($blok['type'] == 'RSS'){
				require_once APP_PATH . 'blok' . DS . 'blokRSS.php';
				$html .= blokRSS($blok, $this);
			}elseif( $blok['name'] == 'Navigation'){
				require_once APP_PATH . 'blok' . DS . $blok['fichier'];
				$html .= $blok['call_fonction']($this, $blok);
			}else{
				require_once APP_PATH . 'blok' . DS . $blok['fichier'];
				$html .= $blok['call_fonction']($this, $blok);
			}
		}
		
		return $html;
	}
	
	public function getListe($code, $order = 'libelle'){
		
		return	$this->db->get(PREFIX . 'commun_liste', array('code =' => $code), $order);
		
	}
	
	public function isIdentified(){
		if( $_SESSION['utilisateur']['id'] == 'Visiteur' )
			return false;
		else
			return true;
	}
	
}

?>