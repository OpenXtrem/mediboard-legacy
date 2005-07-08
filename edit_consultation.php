<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );
require_once( $AppUI->getModuleClass('dPcabinet', 'tarif') );
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'aidesaisie') );

if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$date = mbGetValueFromGetOrSession("date", mbDate()); 

// Utilisateur s�lectionn� ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel");

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$selConsult = mbGetValueFromGetOrSession("selConsult");
if (isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult");
}

$consult = new CConsultation();
if ($selConsult) {
  $consult->load($selConsult);
  $consult->loadRefs();
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  foreach ($patient->_ref_consultations as $key => $value) {
    $patient->_ref_consultations[$key]->loadRefs();
    $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefs();
  }
  foreach ($patient->_ref_consultations_anesth as $key => $value) {
    $patient->_ref_consultations_anesth[$key]->loadRefs();
    $patient->_ref_consultations_anesth[$key]->_ref_plageconsult->loadRefs();
  }
  foreach ($patient->_ref_operations as $key => $value) {
    $patient->_ref_operations[$key]->loadRefs();
  }
  foreach ($patient->_ref_hospitalisations as $key => $value) {
    $patient->_ref_hospitalisations[$key]->loadRefs();
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
  
}

// R�cup�ration des plages de consultation du jour et chargement des r�f�rences
$listPlage = new CPlageconsult();
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$where["date"] = "= '$date'";
$order = "debut";
$listPlage = $listPlage->loadList($where, $order);

$vue = mbGetValueFromGetOrSession("vue2", 0);


foreach($listPlage as $key => $value) {
  $listPlage[$key]->loadRefs();
  foreach($listPlage[$key]->_ref_consultations as $key2 => $value2) {
    if($vue && ($listPlage[$key]->_ref_consultations[$key2]->chrono == CC_TERMINE))
      unset($listPlage[$key]->_ref_consultations[$key2]);
    else
      $listPlage[$key]->_ref_consultations[$key2]->loadRefs();
  }
}

// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon["type"] = "= 'consultation'";
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

// R�cup�ration des aides � la saisie
$where = array();
$where["user_id"] = " = '$userSel->user_id'";
$where["module"] = " = '$m'";
$where["class"] = " = 'Consultation'";

$aidesConsultation = new CAideSaisie();
$aidesConsultation = $aidesConsultation->loadList($where);

// Initialisation to prevent understandable smarty notices
$aides = array(
  "rques" => null,
  "motif" => null,
  "examen" => null,
  "traitement" => null  
);

foreach ($aidesConsultation as $aideConsultation) {
  $aides[$aideConsultation->field][$aideConsultation->text] = $aideConsultation->name;  
}

// R�cup�ration des tarifs
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$tarifsChir = new CTarif;
$tarifsChir = $tarifsChir->loadList($where);
$where = array();
$where["function_id"] = "= '$userSel->function_id'";
$tarifsCab = new CTarif;
$tarifsCab = $tarifsCab->loadList($where);

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->debugging = false;

$smarty->assign('date' , $date );

$smarty->assign('vue', $vue);

$smarty->assign('listPlage', $listPlage);
$smarty->assign('listModelePrat', $listModelePrat);
$smarty->assign('listModeleFunc', $listModeleFunc);
$smarty->assign('aides', $aides);
$smarty->assign('tarifsChir', $tarifsChir);
$smarty->assign('tarifsCab', $tarifsCab);
$smarty->assign('consult', $consult);

$smarty->display('edit_consultation.tpl');

?>