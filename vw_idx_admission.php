<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=public&a=access_denied" );
}

//Initialisation de variables
$listDay = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$listMonth = array("Janvier", "F�vrier", "Mars", "Avril", "Mai", "Juin",
				"Juillet", "Aout", "Septembre", "Octobre", "Novembre", "D�cembre");

$selAff = mbGetValueFromGetOrSession("selAff", 0);
$day = mbGetValueFromGetOrSession("day", date("d"));
$month = mbGetValueFromGetOrSession("month", date("m"));
$year = mbGetValueFromGetOrSession("year", date("Y"));

$nday = date("d", mktime(0, 0, 0, $month, $day + 1, $year));
$ndaym = date("m", mktime(0, 0, 0, $month, $day + 1, $year));
$ndayy = date("Y", mktime(0, 0, 0, $month, $day + 1, $year));
$pday = date("d", mktime(0, 0, 0, $month, $day - 1, $year));
$pdaym = date("m", mktime(0, 0, 0, $month, $day - 1, $year));
$pdayy = date("Y", mktime(0, 0, 0, $month, $day - 1, $year));
$nmonth = date("m", mktime(0, 0, 0, $month + 1, $day, $year));
$nmonthd = date("d", mktime(0, 0, 0, $month + 1, $day, $year));
$nmonthy = date("Y", mktime(0, 0, 0, $month + 1, $day, $year));
$pmonth = date("m", mktime(0, 0, 0, $month - 1, $day, $year));
$pmonthd = date("d", mktime(0, 0, 0, $month - 1, $day, $year));
$pmonthy = date("Y", mktime(0, 0, 0, $month - 1, $day, $year));

$dayOfWeek = date("w", mktime(0, 0, 0, $month, $day, $year));
$dayName = $listDay[$dayOfWeek];
$monthName = $listMonth[$month - 1];
$title1 = "$monthName $year";
$title2 = "$dayName $day $monthName $year";

$sql = "SELECT operation_id, operations.date_adm AS date, count(operation_id) AS num
		FROM operations
		LEFT JOIN plagesop
		ON operations.plageop_id = plagesop.id
		WHERE operations.date_adm LIKE '$year-$month-__'";
if($selAff != "0")
  $sql .= " AND operations.admis = '$selAff'";
$sql .= " GROUP BY operations.date_adm
		  ORDER BY operations.date_adm";
$list = db_loadlist($sql);
foreach($list as $key => $value) {
  $currentDayOfWeek = $listDay[date("w", mktime(0, 0, 0, substr($value["date"], 5, 2), substr($value["date"], 8, 2), substr($value["date"], 0, 4)))];
  $list[$key]["dateFormed"] = $currentDayOfWeek." ".intval(substr($value["date"], 8, 2));
  $list[$key]["day"] = substr($value["date"], 8, 2);
}
$sql = "SELECT operations.operation_id, patients.nom AS nom, patients.prenom AS prenom,
        operations.admis AS admis, users.user_first_name AS chir_firstname,
        users.user_last_name AS chir_lastname, operations.time_adm
		FROM operations
		LEFT JOIN patients
		ON operations.pat_id = patients.patient_id
		LEFT JOIN plagesop
		ON operations.plageop_id = plagesop.id
		LEFT JOIN users
		ON users.user_username = plagesop.id_chir
		WHERE operations.date_adm = '$year-$month-$day'";
if($selAff != "0")
  $sql .= " AND operations.admis = '$selAff'";
$sql .= " ORDER BY operations.time_adm";
$today = db_loadlist($sql);
foreach($today as $key => $value) {
  $today[$key]["hour"] = substr($value["time_adm"], 0, 2)."h".substr($value["time_adm"], 3, 2);
}

// Cr�ation du template
require_once("classes/smartydp.class.php");
$smarty = new CSmartyDP;

$smarty->assign('year', $year);
$smarty->assign('day', $day);
$smarty->assign('nday', $nday);
$smarty->assign('ndaym', $ndaym);
$smarty->assign('ndayy', $ndayy);
$smarty->assign('pday', $pday);
$smarty->assign('pdaym', $pdaym);
$smarty->assign('pdayy', $pdayy);
$smarty->assign('month', $month);
$smarty->assign('nmonthd', $nmonthd);
$smarty->assign('nmonth', $nmonth);
$smarty->assign('nmonthy', $nmonthy);
$smarty->assign('pmonthd', $pmonthd);
$smarty->assign('pmonth', $pmonth);
$smarty->assign('pmonthy', $pmonthy);
$smarty->assign('selAff', $selAff);
$smarty->assign('title1', $title1);
$smarty->assign('title2', $title2);
$smarty->assign('list', $list);
$smarty->assign('today', $today);

$smarty->display('vw_idx_admission.tpl');

?>