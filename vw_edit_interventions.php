<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=public&a=access_denied" );
}

if(!($id = mbGetValueFromGetOrSession('id'))) {
  $AppUI->msg = "Vous devez choisir une plage op�ratoire";
  $AppUI->redirect( "m=dPbloc&tab=1");
}

$anesth = dPgetSysVal("AnesthType");

// Info sur la plag op�ratoire
$sql = "SELECT plagesop.debut AS debut, plagesop.fin AS fin,
        users.user_first_name AS firstname, users.user_last_name AS lastname,
        plagesop.date AS date, sallesbloc.nom AS salle
		FROM plagesop
		LEFT JOIN users
		ON plagesop.id_chir = users.user_username
		LEFT JOIN sallesbloc
		ON plagesop.id_salle = sallesbloc.id
		WHERE plagesop.id = '$id'";
$result = db_loadlist($sql);
$title = $result[0];
$title["plage"] = substr($title["debut"], 0, 2)."h".substr($title["debut"], 3, 2)." - ".substr($title["fin"], 0, 2)."h".substr($title["fin"], 3, 2);

// Liste de droite
$sql = "SELECT operations.operation_id AS id, patients.prenom AS firstname, patients.nom AS lastname,
		patients.naissance AS naissance, operations.type_anesth AS type_anesth,
		operations.CCAM_code AS CCAM_code, operations.temp_operation AS temps, operations.cote AS cote,
        operations.date_adm AS date_adm, operations.time_adm AS time_adm, operations.annulee AS annulee
		FROM operations
		LEFT JOIN patients
		ON operations.pat_id = patients.patient_id
		LEFT JOIN plagesop
		ON operations.plageop_id = plagesop.id
		WHERE plagesop.id = '$id' AND operations.rank = '0'
		ORDER BY operations.temp_operation";
$list1 = db_loadlist($sql);

// Liste de gauche
$sql = "SELECT operations.operation_id AS id, patients.prenom AS firstname, patients.nom AS lastname,
		patients.naissance AS naissance,
		operations.CCAM_code AS CCAM_code, operations.temp_operation AS temps, operations.cote AS cote,
        operations.date_adm AS date_adm, operations.time_adm AS time_adm,
        operations.time_operation AS heure, plagesop.debut AS debut, plagesop.fin AS fin, operations.rank AS rank,
        operations.type_anesth AS type_anesth
		FROM operations
		LEFT JOIN patients
		ON operations.pat_id = patients.patient_id
		LEFT JOIN plagesop
		ON operations.plageop_id = plagesop.id
		WHERE plagesop.id = '$id' AND operations.rank != '0'
		ORDER BY operations.rank";
$list2 = db_loadlist($sql);

$mysql = mysql_connect("localhost", "CCAMAdmin", "AdminCCAM")
  or die("Could not connect");
mysql_select_db("ccam")
  or die("Could not select database");
if(isset($list1)) {
  foreach($list1 as $key => $value) {
    $annais = substr($value["naissance"], 0, 4);
    $anjour = date("Y");
    $moisnais = substr($value["naissance"], 5, 2);
    $moisjour = date("m");
    $journais = substr($value["naissance"], 8, 2);
    $jourjour = date("d");
    $age = $anjour-$annais;
    if($moisjour<$moisnais){$age=$age-1;}
    if($jourjour<$journais && $moisjour==$moisnais){$age=$age-1;}
    $list1[$key]["age"] = $age;
    $sql = "select LIBELLELONG from actes where CODE = '".$value["CCAM_code"]."'";
    $ccamr = mysql_query($sql);
    $ccam = mysql_fetch_array($ccamr);
    $list1[$key]["CCAM"] = $ccam["LIBELLELONG"];
	$list1[$key]["duree"] = substr($value["temps"], 0, 2)."h".substr($value["temps"], 3, 2);
	if($value["type_anesth"])
	  $list1[$key]["lu_type_anesth"] = $anesth[$value["type_anesth"]];
	else
	  $list1[$key]["lu_type_anesth"] = 0;
	$list1[$key]["time_adm"] = substr($value["time_adm"], 0, 2)."h".substr($value["time_adm"], 3, 2);
  }
}
else
  $list1 = "";
if(isset($list2)) {
  foreach($list2 as $key => $value) {
    $annais = substr($value["naissance"], 0, 4);
    $anjour = date("Y");
    $moisnais = substr($value["naissance"], 5, 2);
    $moisjour = date("m");
    $journais = substr($value["naissance"], 8, 2);
    $jourjour = date("d");
    $age = $anjour-$annais;
    if($moisjour<$moisnais){$age=$age-1;}
    if($jourjour<$journais && $moisjour==$moisnais){$age=$age-1;}
    $list2[$key]["age"] = $age;
    $sql = "select LIBELLELONG from actes where CODE = '".$value["CCAM_code"]."'";
    $ccamr = mysql_query($sql);
    $ccam = mysql_fetch_array($ccamr);
    $list2[$key]["CCAM"] = $ccam["LIBELLELONG"];
	$list2[$key]["duree"] = substr($value["temps"], 0, 2)."h".substr($value["temps"], 3, 2);
	$list2[$key]["hour"] = substr($value["heure"], 0, 2);
	$list2[$key]["min"] = substr($value["heure"], 3, 2);
	if($list2[$key]["type_anesth"])
	  $list2[$key]["lu_type_anesth"] = $anesth[$list2[$key]["type_anesth"]];
	else
	  $list2[$key]["lu_type_anesth"] = 0;
	$list2[$key]["time_adm"] = substr($value["time_adm"], 0, 2)."h".substr($value["time_adm"], 3, 2);
    $j = 0;
    for($i = substr($value["debut"], 0, 2) ; $i < substr($value["fin"], 0, 2) ; $i++) {
      if(strlen($i) == 1)
        $i = "0".$i;
	  $list2[$key]["listhour"][$j] = $i;
      $j++;
    }
  }
}
else
  $list2 = "";

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('title', $title);
$smarty->assign('anesth', $anesth);
$smarty->assign('list1', $list1);
$smarty->assign('list2', $list2);
$smarty->assign('max', sizeof($list2));

$smarty->display('vw_edit_interventions.tpl');

?>