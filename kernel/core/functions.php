<?php if( !defined('IN_VA') ) exit;

function getConfig($app){

	if( !$config = $app->cache->get('config') ){
		$tmp = $app->db->get(PREFIX . 'config');
		$cfg = array();
		foreach($tmp as $data){
			$cfg[$data['cle']] = $data['valeur'];
		}
		
		$app->cache->save(serialize($cfg));
		return $cfg;
	}
	
	return unserialize($config);
	
}

function getLink($str){
	global $config;
	
	static $string_url = null;
	
	if( is_null($string_url) ){
		
		if( $config['rewrite_url'] == 1){
			$string_url = $config['url'] . $config['url_dir'];
		}else{
			$string_url = $config['url'] . $config['url_dir'] . 'index.php/';
		}
		/*
		if($config['rewrite_url'] == 1){
			$string_url = 'http://'. $_SERVER['HTTP_HOST'];
			if( isset($_SERVER['REDIRECT_QUERY_STRING']) )
				$string_url .= str_replace($_SERVER['REDIRECT_QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
			else
				$string_url .= $_SERVER['REQUEST_URI'];
		}elseif( !isset($_SERVER['REDIRECT_QUERY_STRING']) ){
			$string_url = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] .'/';
		}else{
			$string_url = 'http://'. $_SERVER['HTTP_HOST'];
			if( isset($_SERVER['REDIRECT_QUERY_STRING']) )
				$string_url .= str_replace($_SERVER['REDIRECT_QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
			else
				$string_url .= $_SERVER['REQUEST_URI'];
		}*/
	}
		//echo $string_url;
	return $string_url . $str;
}

/**
*	getUniqueID
*	Genere un ID unique
*	@return string Id unique
* */
function getUniqueID(){
	return md5(uniqid(rand(), true));
}

function redirect($url, $tps = 0, $message = ''){

    $temps = $tps * 1000;

    if($message != ''){
		
		global $smarty;
		
        $r = explode('|',$message);
        $text = $r[0];

        if( isset($r[1]) ){
            $affichage = $r[1];
        }else{
            $affichage = 'success';
        }
		
        $smarty->assign('error_class', 'error_' . $affichage);
        $smarty->assign('error_image', 'comment_' . $affichage);
        $smarty->assign('message', $text);
        $smarty->assign('url', $url);
        $smarty->assign('temp', $temps);
        return $smarty->fetch('redirect.tpl');
    }else{

		echo "<script type=\"text/javascript\">\n"
		. "<!--\n"
		. "\n"
		. "function redirect() {\n"
		. "window.location='" . $url . "'\n"
		. "}\n"
		. "setTimeout('redirect()','" . $temps ."');\n"
		. "\n"
		. "// -->\n"
		. "</script>\n";
		exit;
    }
}

function hash_password($password){
	return sha1($password);
}

/**
 * Verifie la synthase d'une adresse email
 * @param string $adresse email a verifie
 * @return bool 
 * */
function VerifieAdresseMail($adresse)
{
   $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
   if(preg_match($Syntaxe,$adresse))
      return true;
   else
     return false;
}

/**
*	Foction facilitant l'envoie d'un mail a l aide de phpmailer
*   @param mixed $destinataire
*   @param string $expediteur
*   @param string $sujet
*   @param string $message
*   @param string $messageHTML
*   @param string $file
*   @return bool
*/
function sendEmail($destinataire, $expediteur, $sujet, $message, $messageHTML = '', $file = ''){
	global $config;
	
	if( !ini_get('safe_mode') ){ set_time_limit(600); }	// Si possible on augmente le tps d excution
	
	// Inclusion Class
	require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'class.phpmailer.php';
	
	// Creation instance
	$mail = new PHPMailer();
	
	$mail->CharSet = 'UTF-8';		

	// Traitement method d envoie
	if( $config['mail_method'] == 'smtp' && $config['smtp_server'] != '' ){
		// Parametrage pour utilisation SMTP
		$mail->IsSMTP();
		$mail->Host = $config['smtp_server'];
		$mail->Port = $config['smtp_port'];

		// Si champ identifiant et mdp smtp utilisation authentification
		if( $config['smtp_login'] != '' && $config['smtp_password'] != '' ){
			$mail->SMTPAuth = true;
			$mail->Username = $config['smtp_login'];
			$mail->Password = $config['smtp_password'];
		}
	}
	
	// Adresse reponse
	$mail->AddReplyTo($expediteur);
	$mail->SetFrom($expediteur);

	// destinataire
	if( is_array($destinataire) ){
		foreach($destinataire as $k => $email){
			$mail->AddAddress($expediteur);
			$mail->AddBCC($email);
		}
	}else{
		$mail->AddAddress($destinataire);
	}

	//Message
	$mail->Subject    = $sujet;
	$mail->AltBody    = $message;
	
	// Fichier joint
	if($file != ''){
		$mail->AddAttachment($file);
	}
	
	if($messageHTML != '')
		$mail->MsgHTML($messageHTML);
	else
		$mail->MsgHTML(nl2br($message));

	// Envoie
	if(!$mail->Send())
		return false;
	else
		return true;  
}

function getFilesInDir($dirname, $path_return = ''){
    
    // On verifie que le dossier existe
    if( !is_dir($dirname) ){
        return false;
    }
    // On ouvre le dossier
    $dir = opendir($dirname);
    // Init du tableau
    $files = array();
    // On liste les fichier
    while($file = readdir($dir)){
        if($file != '.' && $file != '..' && !is_dir($dirname.$file) && $file != ' '){
            $files[$file] = $path_return . $file;
        }
    }
    // On ferme le dossier
    closedir($dir);
    // On retourne les fichiers
    return $files;
}

/**
 * Contruit la pagination
 * @param array $param : perPage => Nombre d element sur une page | fileName => Url pour la pagination (ex : index.php?c=controller&page=%d) | totalItems => Nombre d element total
 * @return mixed Code html contenant la pagination
 * */
function getPagination($param){
	global $config;
	
	require_once ROOT_PATH .'kernel'. DS .'lib'. DS .'PEAR'. DS .'Pager.php';

	if( strstr($_SERVER['REQUEST_URI'], 'adm/') == TRUE)
		$param['fileName'] = str_replace($config['url'] . $config['url_dir'] . 'adm/', '', $param['fileName']);
	else
		$param['fileName'] = str_replace($config['url'] . $config['url_dir'], '', $param['fileName']);
	
	/*if( isset($_GET['page']))
	$param['fileName'] = str_replace('flight?page='. $_GET['page'], '', $param['fileName']);*/
	
	if( $config['rewrite_url'] == 1)
		$param['fileName'] = str_replace('index.php/', '', $param['fileName']);
	
	if( $config['url_dir'] == '' )
		$path = substr($config['url'],0,-1);
	else
		$path = $config['url'] . substr($config['url_dir'],0,-1);
		
    $pager_option = array(
    'mode'  =>  'Sliding',
    'perPage'   =>  $param['perPage'],
    'append' => false,
    'delta' =>  2,
    'urlVar' =>  'page',
	'path'	=>	$path,
    'fileName'  =>  $param['fileName'],
    'totalItems'    =>  $param['totalItems'] );

    $pager = & Pager::factory($pager_option);
    $links = $pager->getLinks();
	
    return $links['all'];
}

/**
 * Calcul l offset pour les requetes SQL
 * Pour la page courante cette fonction est lien a la fonction getPagination
 * @param int $limit	
 * @return int $offset
 * */
function getOffset($limit = 30){
    if(isset($_GET['page']))
        $current_page = $_GET['page'];
    else
        $current_page = 1;
    
    // Calcul du offset
    $offset = $current_page * $limit - $limit;
    
    return $offset;
}

/**
 * getChaine
 * Genere une chaine aleatoire
 *
 * @param int $long longueur de la chaine
 * @return string $str chaine aleatoire
 * */
function getChaine($long = 8){
    $chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = '';
    for($u = 1; $u <= $long; $u++) {
        $nb = strlen($chaine);
        $nb = mt_rand(0,($nb-1));
        $str .= $chaine[$nb];
    }
    return $str;
}

function clearHtmlCode($code){
    $code = trim($code);
    $code = str_replace("\r", '', $code);
    $code = str_replace("\n", '', $code);
    $code = str_replace("\t", '', $code);
    return $code;
}


function getUrlForPagination(){
	if( isset($_GET['page']) )
        // On supprime le page=page_courant et et recupere les parametre d appel
        $url = str_replace('&page=' . $_GET['page'],'',$_SERVER['QUERY_STRING']); 
    else 
        // On recupere les parametres d appele, ce qui permet d inclure les parametres de recherche et de tri
        $url = $_SERVER['QUERY_STRING'];
        
    return $url;
}

/**
*	Function permettant de savoir si le visiteur courant est un ROBOTS
*	Code inspirer de phpBB3
*	@return bool false si non robot
*/
function isBot(){
	global $registry;

	$browser = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
	$ip = (!empty($_SERVER['REMOTE_ADDR'])) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	$bot = false;

	if( !$bots = $registry->cache->get('bots_listes') ){
		$bots = $registry->db->get(PREFIX . 'bots');
		$registry->cache->save(serialize($bots));
	}else{
		$bots = unserialize($bots);
	}
	
	foreach($bots as $row){
	
		if ($row['bot_agent'] && preg_match('#' . str_replace('\*', '.*?', preg_quote($row['bot_agent'], '#')) . '#i', $browser)){
			$bot = $row['user_id'];
		}
		
		// If ip is supplied, we will make sure the ip is matching too...
		if ($row['bot_ip'] && ($bot || !$row['bot_agent']))
		{
			// Set bot to false, then we only have to set it to true if it is matching
			$bot = false;

			foreach (explode(',', $row['bot_ip']) as $bot_ip)
			{
				$bot_ip = trim($bot_ip);

				if (!$bot_ip)
				{
					continue;
				}

				if (strpos($ip, $bot_ip) === 0)
				{
					$bot = (int) $row['user_id'];
					break;
				}
			}
		}
	}
	
	if( $bot !== false) return true;
	
	return false;	
}

function injectBotIp(){
	global $registry;
	
	$fichiers = getFilesInDir(ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Bots' . DS);
	foreach($fichiers as $file){
		$ips =  @file(ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Bots' . DS . $file);
		$listes = '';
		foreach($ips as $ip){
			$ip = trim($ip);
			$listes .= $ip .',';
		}
		$registry->db->update(PREFIX . 'bots', array('bot_ip' => substr($listes,0,-1)), array('bot_agent =' => $file) );
	}
}

/**
*	Convertie un nombre de seconde au format HH:MM:SS
*	@param int $value nombre de seconde
*	@return string chaine formaté
*/
function secondesInDuree($value){

	//initialisation
	$secondes = 0;
	$minutes = 0;
	$heure = 0;
	
	//convertion
	$minutes = $value/60; 
	$secondes = bcmod($value,"60");
	$minutes = floor($minutes);
	
	while($secondes >= "60") //ajoute une minute toutes les 60 secondes
	{
	 	$secondes = $secondes-60;
	 	$minutes++;
	}
	
	while($minutes >= "60")//ajoute une heure toutes les 60 minutes
	{
	 	$minutes = $minutes-60;
	 	$heure++;
	}
	
	if($minutes < "10") // ajoute le deuxieme 0 pour la prÃ©sentation
	{
		$minutes = "0".$minutes;
	}
	
	if($secondes < "10") // ajoute le deuxieme 0 pour la prÃ©sentation
	{
		$secondes = "0".$secondes;
	}
	
	if($heure < "10") // ajoute le deuxieme 0 pour la prÃ©sentation
	{
		$heure = "0".$heure;
	}
	
	return $heure.":".$minutes.":".$secondes; //contient le rÃ©sultat final
}

/**
*	Verifie que la chaine passé est conforme a la structure des duree
*	@param str $str Heures:Minutes 01:30
*	@return bool
*/
function validHeure($str){
	$h = explode(':', $str);
	
	if( count($h) != 2)
		return false;
	
	if( $h[0] > 24)
		return false;
		
	if( $h[1] > 59)
		return false;
		
	return true;
}

/**
*	Verifie que la chaine passé est conforme a la structure des duree
*	@param str $str Heures:Minutes 01:30
*	@return bool
*/
function validDuration($str){
	$h = explode(':', $str);
	
	if( count($h) != 2)
		return false;
		
	if( $h[1] > 59)
		return false;
		
	return true;
}

/**
*	[0] = lat
*	[1] = lon
*/
function getDistance2pts($coord1, $coord2){
	$r = 6366;

	$coord1[0] = deg2rad($coord1[0]);
	$coord1[1] = deg2rad($coord1[1]);
	$coord2[0] = deg2rad($coord2[0]);
	$coord2[1] = deg2rad($coord2[1]);
	
	$ds= acos(sin($coord1[0])*sin($coord2[0])+cos($coord1[0])*cos($coord2[0])*cos($coord1[1]-$coord2[1]));
	
	$dpr = $ds * $r;
	
	return $dpr * 0.54;	// return une distance en nm	
}

/**
*	Recupere la liste des themes valides dans le dossier
*	@return mixed $result tableau le nom des themes disponibles
*/
function getThemes(){
	$result = array();
	
	$handle = @opendir(ROOT_PATH . 'themes');
	
	while (false !== ($f = readdir($handle))){
		if ($f != "." && $f != ".." && $f != "CVS" && $f != "index.html" && !preg_match("`[.]`", $f)){
			if( is_file(ROOT_PATH . 'themes' . DS . $f . DS . 'layout.tpl') ){
				$result[] = $f;
			}
        }
	}
    closedir($handle);

    return $result;
}

function FormatDateToTimestamp($date){
	$tmp = explode('/',$date);
	return mktime(0,0,0,$tmp[1],$tmp[0],$tmp[2]);	
}

function FormatDateToMySQL($date){
	$tmp = explode('/',$date);
	return $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];	
}

function FormatDateFromMySql($date){
	$tmp = explode('-',$date);
	return $tmp[2] . '/' . $tmp[1] . '/' . $tmp[0];
}

function cryptPassword($password, $identifiant){
	return sha1( sha1(strtolower($identifiant)) . $password );
}