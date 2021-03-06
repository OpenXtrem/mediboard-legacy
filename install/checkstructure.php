<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("header.php");
require_once("mbdb.class.php");

$dbConfig = $dPconfig["db"]["std"];
$db = new CMbDb(
  $dbConfig["dbhost"], 
  $dbConfig["dbuser"], 
  $dbConfig["dbpass"], 
  $dbConfig["dbname"]);
$db->connect();
if (!$db->getOne("SELECT * FROM `users`")) {
  showHeader();
?>

<div class="big-error">
  Erreur : la structure de la base de donn�es principale n'a pas �t� construite, il est
  donc impossible de finaliser l'installation.
  <br />Retourner � l'�tape pr�c�dente pour construire la structure.
</div>

<?php
  showFooter();
  die();
}
?>
