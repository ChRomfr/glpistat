<?php
/**
 * @Author : Drouche Romain
 * @Email : roumain18@gmail.com
 */

$chrono1 = microtime();
define('IN_VA', TRUE);
define('ROOT_PATH', str_replace('index.php','',__FILE__));
define('DS', DIRECTORY_SEPARATOR); 
define('APP_PATH', ROOT_PATH . 'app' . DS);
define('CACHE_PATH', ROOT_PATH . 'cache' . DS);
define('VIEW_PATH', ROOT_PATH . 'app' . DS . 'view' . DS);
define('CONTROLLER_PATH', ROOT_PATH . 'app' . DS . 'controller' . DS);
define('MODEL_PATH', ROOT_PATH . 'app' . DS . 'model' . DS);
define('ADM_MODEL_PATH','');


// INCLUSION DU NOYAU
require_once ROOT_PATH . 'kernel' . DS . 'core'. DS . 'core.php';

$registry->config = $config;

$registry->smarty->assign('config',$config);

//	APL des JS et CSS supplementaire
$jquery_theme = 'overcast';
$registry->addJS('jquery-1.9.1.min.js');
$registry->addJS('jquery-migrate-1.1.1.min.js');
$registry->addJS('jquery-ui-1.9.2.custom.min.js');
$registry->addCSS($jquery_theme . '/jquery-ui-1.9.2.custom.min.css');

// DEFINTION CHEMIN APPLICATION
$registry->router->setPath(array(ROOT_PATH . 'MyApp' . DS . 'controller' .DS,  ROOT_PATH . 'app' . DS . 'controller' .DS) );

// EXECUTION DU SCRIPT
$Content = $registry->router->loader();

// GESTION AFFICHAGE
if( !$registry->HTTPRequest->getExists('nohtml') ){
    $registry->smarty->assign('app', $registry);
	$registry->smarty->assign('css_add', registry::$css);
	$registry->smarty->assign('js_add', registry::$js);
	$registry->smarty->assign('content', $Content);
	echo $registry->smarty->display(ROOT_PATH . 'themes' . DS . $config['theme'] . DS . 'layout.tpl');
	
}else
	echo $Content;

if( IN_PRODUCTION == false && !$registry->HTTPRequest->getExists('nohtml')){
	// Affichage du debug en pied de page
	echo	'<div style="size:9px; margin:auto; width:1000px;">';
	echo	'<div>
			Page generee en : '. round( microtime() - $chrono1, 6) . ' sec | 
			Requete SQL : '. $db->num_queries .' | 
			Utilisation memoire : ' . round(memory_get_usage() / (1024*1024),2) .' mo
			</div>';
	
	echo	'<div style="margin:auto; width:1000px;"><hr/>SESSION :';		
	var_dump($_SESSION);
	echo	'<hr/>SERVER :';		
	var_dump($_SERVER);
	echo	'<hr/>POST :<pre><small>'; 
	print_r($_POST);
	echo	'<hr/>Requetes :<pre>';
	print_r($registry->db->queries);
	echo 	'<hr/>CONFIG :<pre><small>';
	print_r($config);
	echo"<hr/>Template<pre><small>"; 
	print_r($registry->smarty->tpls_used);
	echo	'</small></pre></div>';
}