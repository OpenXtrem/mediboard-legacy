<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=public&a=access_denied" );
}

// R�cup�ration des fonctions
$sql = "SELECT functions_mediboard.*, groups_mediboard.text AS mygroup
  FROM functions_mediboard, groups_mediboard
  WHERE functions_mediboard.group_id = groups_mediboard.group_id
  ORDER BY groups_mediboard.text, functions_mediboard.text";
$functions = db_loadList($sql);

// R�cup�ration de la fonction � ajouter/editer
if (isset($_GET["userfunction"])) {
  $_SESSION[$m][$tab]["userfunction"] = $_GET["userfunction"];
}

$userfunction = dPgetParam($_SESSION[$m][$tab], "userfunction", 0);

$sql = "SELECT functions_mediboard.*, groups_mediboard.text AS mygroup
  FROM functions_mediboard, groups_mediboard
  WHERE function_id = '$userfunction'
  AND functions_mediboard.group_id = groups_mediboard.group_id";
$result = db_exec($sql);
$functionsel = db_fetch_array($result);
$functionsel["exist"] = $userfunction;

// R�cup�ration des groupes
$sql= "SELECT * 
  FROM groups_mediboard 
  ORDER BY text";
$groups = db_loadList($sql);

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('functions', $functions);
$smarty->assign('functionsel', $functionsel);
$smarty->assign('groups', $groups);

$smarty->display('vw_idx_functions.tpl');

?>