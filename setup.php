<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPcabinet';
$config['mod_version'] = '0.25';
$config['mod_directory'] = 'dPcabinet';
$config['mod_setup_class'] = 'CSetupdPcabinet';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Cabinet';
$config['mod_ui_icon'] = 'dPcabinet.png';
$config['mod_description'] = 'Gestion de cabinet de consultation';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPcabinet {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPcabinet&a=configure' );
  		return true;
	}

	function remove() {
      db_exec( "DROP TABLE consultation;" );
      db_exec( "DROP TABLE plageconsult;" );
      db_exec( "DROP TABLE files_mediboard;" );
      db_exec( "DROP TABLE files_index_mediboard;" );
      db_exec( "DROP TABLE tarifs;" );

		return null;
	}

	function upgrade( $old_version ) {
		switch ( $old_version )
		{
		case "all":
		case "0.1":
		  $sql = "ALTER TABLE plageconsult ADD freq TIME DEFAULT '00:15:00' NOT NULL AFTER date ;";
		  db_exec( $sql ); db_error();
		case "0.2":
          $sql = "ALTER TABLE consultation ADD compte_rendu TEXT DEFAULT NULL";
          db_exec( $sql ); db_error();
        case "0.21":
          $sql = "ALTER TABLE consultation CHANGE duree duree TINYINT DEFAULT '1' NOT NULL ";
          db_exec( $sql ); db_error();
          $sql = "UPDATE consultation SET duree='1' ";
          db_exec( $sql ); db_error();
        case "0.22":
          $sql = "ALTER TABLE `consultation` " .
              "\nADD `chrono` TINYINT DEFAULT '16' NOT NULL," .
              "\nADD `annule` TINYINT DEFAULT '0' NOT NULL," .
              "\nADD `paye` TINYINT DEFAULT '0' NOT NULL," .
              "\nADD `cr_valide` TINYINT DEFAULT '0' NOT NULL," .
              "\nADD `examen` TEXT," .
              "\nADD `traitement` TEXT";
          db_exec( $sql ); db_error();
        case "0.23":
          $sql = "ALTER TABLE `consultation` ADD `premiere` TINYINT NOT NULL";
          db_exec( $sql ); db_error();
        case "0.24":
          $sql = "CREATE TABLE `tarifs` (
                  `tarif_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT DEFAULT '0' NOT NULL ,
                  `function_id` BIGINT DEFAULT '0' NOT NULL ,
                  `description` VARCHAR( 50 ) ,
                  `valeur` TINYINT,
                  PRIMARY KEY ( `tarif_id` ) ,
                  INDEX ( `chir_id` , `function_id` )
                  ) COMMENT = 'table des tarifs de consultation';";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `consultation` ADD `tarif` TINYINT,
                  ADD `type_tarif` ENUM( 'cheque', 'CB', 'especes', 'tiers', 'autre' ) ;";
          db_exec( $sql ); db_error();
        case "0.25":
			return true;
		default:
			return false;
		}
		return false;
	}

	function install() {
		$sql = "CREATE TABLE consultation (
                consultation_id bigint(20) NOT NULL auto_increment,
                plageconsult_id bigint(20) NOT NULL default '0',
                patient_id bigint(20) NOT NULL default '0',
                heure time NOT NULL default '00:00:00',
                duree time NOT NULL default '00:00:00',
                motif text,
                secteur1 smallint(6) NOT NULL default '0',
                secteur2 smallint(6) NOT NULL default '0',
                rques text,
                PRIMARY KEY  (consultation_id),
                KEY plageconsult_id (plageconsult_id,patient_id)
                ) TYPE=MyISAM COMMENT='Table des consultations';";
		db_exec( $sql ); db_error();
		$sql = "CREATE TABLE plageconsult (
                plageconsult_id bigint(20) NOT NULL auto_increment,
                chir_id bigint(20) NOT NULL default '0',
                date date NOT NULL default '0000-00-00',
                debut time NOT NULL default '00:00:00',
                fin time NOT NULL default '00:00:00',
                PRIMARY KEY  (plageconsult_id),
                KEY chir_id (chir_id)
                ) TYPE=MyISAM COMMENT='Table des plages de consultation des m�decins';";
		db_exec( $sql ); db_error();
		$sql = "CREATE TABLE files_mediboard (
                file_id int(11) NOT NULL auto_increment,
                file_real_filename varchar(255) NOT NULL default '',
                file_consultation bigint(20) NOT NULL default '0',
                file_operation bigint(20) NOT NULL default '0',
                file_name varchar(255) NOT NULL default '',
                file_parent int(11) default '0',
                file_description text,
                file_type varchar(100) default NULL,
                file_owner int(11) default '0',
                file_date datetime default NULL,
                file_size int(11) default '0',
                file_version float NOT NULL default '0',
                file_icon varchar(20) default 'obj/',
                PRIMARY KEY  (file_id),
                KEY idx_file_consultation (file_consultation),
                KEY idx_file_operation (file_operation),
                KEY idx_file_parent (file_parent)
              ) TYPE=MyISAM;";
		db_exec( $sql ); db_error();
        $sql = "CREATE TABLE files_index_mediboard (
                file_id int(11) NOT NULL default '0',
                word varchar(50) NOT NULL default '',
                word_placement int(11) default '0',
                PRIMARY KEY  (file_id,word),
                KEY idx_fwrd (word),
                KEY idx_wcnt (word_placement)
                ) TYPE=MyISAM;";
		db_exec( $sql ); db_error();
    $this->upgrade("all");
		return null;
	}
}

?>