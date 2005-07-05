<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPbloc';
$config['mod_version'] = '0.11';
$config['mod_directory'] = 'dPbloc';
$config['mod_setup_class'] = 'CSetupdPbloc';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Planning Bloc';
$config['mod_ui_icon'] = 'dPbloc.png';
$config['mod_description'] = 'Gestion du bloc op�ratoire';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPbloc {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPbloc&a=configure' );
  		return true;
	}

	function remove() {
		db_exec( "DROP TABLE plagesop;" );
		db_exec( "DROP TABLE sallesbloc;" );
		return null;
	}


	function upgrade( $old_version ) {

		switch ( $old_version )
		{
		case "all":
		case "0.1":
		  $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_chir` );";
		  db_exec( $sql ); db_error();
		  $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_anesth` )";
		  db_exec( $sql ); db_error();
		  $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_spec` )";
		  db_exec( $sql ); db_error();
		  $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_salle` )";
		  db_exec( $sql ); db_error();
		  $sql = "ALTER TABLE `plagesop` ADD INDEX ( `date` )";
		  db_exec( $sql ); db_error();
		case "0.11":
			return true;
		default:
			return false;
		}
		return false;
	}

	function install() {
		$sql = "CREATE TABLE plagesop (
					id bigint(20) NOT NULL auto_increment,
					id_chir varchar(20) NOT NULL default '0',
					id_anesth varchar(20) default NULL,
					id_spec tinyint(4) default NULL,
					id_salle tinyint(4) NOT NULL default '0',
					date date NOT NULL default '0000-00-00',
					debut time NOT NULL default '00:00:00',
					fin time NOT NULL default '00:00:00',
					PRIMARY KEY  (id)
					) TYPE=MyISAM COMMENT='Table des plages d''op�ration';";
		db_exec( $sql ); db_error();
		$sql = "CREATE TABLE sallesbloc (
  					id tinyint(4) NOT NULL auto_increment,
					nom varchar(50) NOT NULL default '',
					PRIMARY KEY  (id)
					) TYPE=MyISAM COMMENT='Table des salles d''op�ration du bloc' AUTO_INCREMENT=6 ;";
		db_exec( $sql ); db_error();
		$this->upgrade("all");
		return null;
	}
}

?>