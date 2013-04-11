<?php if( !defined('IN_VA') ) exit;

// DONNEE DE CONNEXION A LA BASE DE DONNEES

$DB_Configuration = array(
'type'			=> 'mysql',
'serveur' 		=> '127.0.0.1',
'utilisateur' 	=> 'root',
'password' 		=> '',
'base' 			=> 'glpi',
);

// Prefix des tables
define('PREFIX', '');	

// Active le mode developpeur		
define('IN_PRODUCTION', false);	

$config = array(
'format_date_day'           =>  "%d/%m/%Y",             // Format de date utilisable
'format_date'				=>	"%d/%m/%Y - %H:%M",     // Format de date utilisable
'rewrite_url'               =>  0,                      // Permet l activation de l URL REWRITING
'url'						=>	'http://127.0.0.1/',
'url_dir'					=>	'glpistat/',
'theme'                     =>  'default',              // Defini le theme 
'navigation_show_ss_menu'	=>	true,	                // Permet d affiche ou non les sous menus dans la navigation   
'glpi_url'					=>	'http://url_glpi/',

'hauteur_graph_categorie'	=>	3000,
'hauteur_graph_sites'		=>	6000,
);

define('USE_TABLE_CONFIG', false);
define('BREAD',1);

$baseUrl = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
$baseUrl .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : getenv('HTTP_HOST');
$baseUrl .= isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : dirname(getenv('SCRIPT_NAME'));

$config['base_url'] = $baseUrl .'/';