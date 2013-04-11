<?php if( !defined('IN_VA') ) exit;
/**
*	GERE LA CONNEXION A LA BASE DE DONNEE EN FONCTION DU TYPE
*/

require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'DB' . DS . 'EPDO.php';		// Inclusion sur charge 
require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'DB' . DS . 'Db.class.php';	// Inclusion bibliotheque requete

switch( $DB_Configuration['type'] ){
	
	case 'mysql':
		require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'DB' . DS . 'mysql.php';
		$dsn = 'mysql:host=' . $DB_Configuration['serveur'] .'; dbname='. $DB_Configuration['base'];
		try{
			//$db = Db_mysql::getInstance($dsn, $DB_Configuration['utilisateur'], $DB_Configuration['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$db = Db_mysql::getInstance($dsn, $DB_Configuration['utilisateur'], $DB_Configuration['password']);
		}
		
		catch (Exception $e){
			echo $e->getMessage();
			echo '<div><p>Erreur de connexion à la base de données</p></div>';
			exit;
		}
		break;	
		
	case 'postgre':
				
		require_once ROOT_PATH . 'kernel' . DS . 'core' . DS . 'DB' . DS . 'postgre.php';
		
		$dsn = 'pgsql:host=' . $DB_Configuration['serveur'] .';port=5432;dbname='. $DB_Configuration['base'];
		
		try{
			//$db = Db_postgre::getInstance($dsn, $DB_Configuration['utilisateur'], $DB_Configuration['password'], $DB_Configuration['schema']);
			$db = new Db_postgre($dsn, $DB_Configuration['utilisateur'], $DB_Configuration['password'], $DB_Configuration['schema']);
		}
		
		catch (Exception $e){
			echo '<div><p>Erreur de connexion à la base de données</p></div>';
			exit;
		}
		var_dump($db);
		$db->setSchema($DB_Configuration['schema']);
		break;
		
	default:
		echo '<div style="text-align:center;"><strong>Moteur de base de données inconnu !</strong></div>';
		exit;
		break;
	
}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);