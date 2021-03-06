<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcompteRendu', 'pack'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu'));
require_once( $AppUI->getModuleClass('mediusers', 'mediusers'));

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Utilisateur s�lectionn� ou utilisateur courant
$user_id = mbGetValueFromGetOrSession("filter_user_id", $AppUI->user_id);

$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : $AppUI->user_id);
$userSel->loadRefs();

if (!$userSel->isPraticien()) {
  $userSel->load(null);
}

// Utilisateurs modifiables
$users = new CMediusers;
$users = $users->loadPraticiens(PERM_EDIT);

// Filtres sur la liste des packs
$where = null;

if ($userSel->user_id) {
	$where["chir_id"] = "= '$userSel->user_id'";
} else {
  $inUsers = array();
  foreach($users as $key => $value) {
    $inUsers[] = $key;
  }
  $where ["chir_id"] = "IN (".implode(",", $inUsers).")";
}

$packs = new CPack();
$packs = $packs->loadList($where);
foreach($packs as $key => $value) {
  $packs[$key]->loadRefsFwd();
}

// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon[] = "type = 'hospitalisation' OR type = 'operation'";
$order = "nom";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = "= '$userSel->user_id'";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = "= '$userSel->function_id'";
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// pack s�lectionn�
$pack_id = mbGetValueFromGetOrSession("pack_id");
$pack = new CPack();
$pack->load($pack_id);
if($pack_id) {
  $pack->loadRefsFwd();
} else {
  $pack->chir_id = $userSel->user_id;
}

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('users', $users);
$smarty->assign('user_id', $user_id);
$smarty->assign('listModelePrat', $listModelePrat);
$smarty->assign('listModeleFunc', $listModeleFunc);
$smarty->assign('packs', $packs);
$smarty->assign('pack', $pack);

$smarty->display('vw_idx_packs.tpl');

?>