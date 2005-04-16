<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPhospi", "service"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if (!$canRead) {
  $AppUI->redirect( "m=public&a=access_denied" );
}

$year  = mbGetValueFromGetOrSession("year" , date("Y"));
$month = mbGetValueFromGetOrSession("month", date("m"));
$day   = mbGetValueFromGetOrSession("day"  , date("d"));
$date = ($year and $month and $day) ? 
  date("Y-m-d", mktime(0, 0, 0, $month+1, $day, $year)) : 
  date("Y-m-d");

// R�cup�ration du service � ajouter/�diter
$serviceSel = new CService;
$serviceSel->load(mbGetValueFromGetOrSession("service_id"));

// R�cup�ration des chambres/services
$services = new CService;
$services = $services->loadList();
foreach ($services as $service_id => $service) {
  $services[$service_id]->loadRefs();
  $chambres =& $services[$service_id]->_ref_chambres;
  foreach ($chambres as $chambre_id => $chambre) {
    $chambres[$chambre_id]->loadRefs();
    $lits =& $chambres[$chambre_id]->_ref_lits;
    foreach ($lits as $lit_id => $lit) {
      $lits[$lit_id]->loadAffectations($date);
      $affectations =& $lits[$lit_id]->_ref_affectations;
      foreach ($affectations as $affectation_id => $affectation) {
        $affectations[$affectation_id]->loadRefs();
        $affectations[$affectation_id]->checkDaysRelative($date);

        $aff_prev =& $affectations[$affectation_id]->_ref_prev;
        if ($aff_prev->affectation_id) {
          $aff_prev->loadRefsFwd();
          $aff_prev->_ref_lit->loadRefsFwd();
        }

        $aff_next =& $affectations[$affectation_id]->_ref_next;
        if ($aff_next->affectation_id) {
          $aff_next->loadRefsFwd();
          $aff_next->_ref_lit->loadRefsFwd();
        }

        $operation =& $affectations[$affectation_id]->_ref_operation;
        $operation->loadRefsFwd();
        $operation->_ref_chir->loadRefsFwd();
      }
    }

    $chambres[$chambre_id]->checkChambre();
  }
}

// R�cup�ration des admissions � affecter
$leftjoin = array(
  "affectation"     => "operations.operation_id = affectation.operation_id",
  "users_mediboard" => "operations.chir_id = users_mediboard.user_id"
);
$ljwhere = "affectation.affectation_id IS NULL";
$order = "users_mediboard.function_id, duree_hospi DESC";

// Admissions du jour
$where = array(
  "date_adm" => "= '$date'",
  "type_adm" => "!= 'exte'",
  $ljwhere  
);
$opNonAffecteesJour = new COperation;
$opNonAffecteesJour = $opNonAffecteesJour->loadList($where, $order, null, null, $leftjoin);

foreach ($opNonAffecteesJour as $op_id => $op) {
  $opNonAffecteesJour[$op_id]->loadRefs();
  $opNonAffecteesJour[$op_id]->_ref_chir->loadRefsFwd();
}

// Admissions ant�rieures
$where = array(
  "'$date' BETWEEN ADDDATE(`date_adm`, INTERVAL 1 DAY) AND ADDDATE(`date_adm`, INTERVAL `duree_hospi` DAY)",
  "affectation.affectation_id IS NULL"
);
$opNonAffecteesAvant = new COperation;
$opNonAffecteesAvant = $opNonAffecteesAvant->loadList($where, $order, null, null, $leftjoin);

foreach ($opNonAffecteesAvant as $op_id => $op) {
  $opNonAffecteesAvant[$op_id]->loadRefs();
  $opNonAffecteesAvant[$op_id]->_ref_chir->loadRefsFwd();
}

$groupOpNonAffectees = array(
  "jour"  => $opNonAffecteesJour ,
  "avant" => $opNonAffecteesAvant
);

// Cr�ation du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->debugging = false;
$smarty->assign('year' , $year );
$smarty->assign('month', $month);
$smarty->assign('day'  , $day  );
$smarty->assign('services', $services);
$smarty->assign('groupOpNonAffectees' , $groupOpNonAffectees);

$smarty->display('vw_affectations.tpl');

?>