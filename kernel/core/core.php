<?php if( !defined('IN_VA') ) exit;

session_start();	// Demarage des sessions
require_once ROOT_PATH . 'config' . DS . 'config.php';

require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'DB' . DS . 'connexion.php';	    // Connexion à la base de données
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Controller.class.php'; 	        // Chargement du fichier controller
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Model.class.php'; 		        // Chargement base model
require_once ROOT_PATH . 'app' . DS . 'local' . DS . 'french.php';	                    // Chargement du fichier lang
require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'MyCache.class.php';              // Chargement class cache
require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'smarty' . DS . 'smarty.php';	    // Chargement de smarty
require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'MySmarty.class.php';	            // Chargement de smarty
require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'Form.class.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'HTTPRequest.class.php';         // Chargement du controller HTTP
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Session.class.php';             // Chargement du controller SESSION
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'functions.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'function_error.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'function_upload.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Registry.class.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Router.class.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Record.class.php';
//require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Log.class.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'version.php';
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Tree.class.php';
//require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'Upload.class.php';

define('BASE_APP_PATH', ROOT_PATH . 'kernel' . DS . 'base_app' . DS);

$registry = new Registry();
$registry->router = new Router($registry);
$registry->session = new Session($registry);
$registry->cache = new MyCache($registry);
$registry->db = Db::getInstance();
$registry->smarty = new MySmarty($registry);
//$registry->log = new log($registry);
$registry->HTTPRequest = new HTTPRequest($registry);
$registry->form = new Form($registry);

$Session = new Session();

//
// VERIFICATION SESSION
// Si utilisateur non loggé on charge le formulaire de connexion


//$registry->smarty->assign('config', $config_general);
$registry->smarty->assign('lang', $lang);

if( USE_TABLE_CONFIG )
    $config = array_merge($config, getConfig($registry) );

$cache = $registry->cache;

if( $registry->router->controller == 'utilisateur' ) require_once ROOT_PATH . 'kernel' . DS . 'base_app' . DS . 'controller' . DS . 'BaseUtilisateurController.php';  
if( $registry->router->controller == 'connexion' ) require_once ROOT_PATH . 'kernel' . DS . 'base_app' . DS . 'controller' . DS . 'BaseConnexionController.php';