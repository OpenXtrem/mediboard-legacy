<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("dPcabinet", "consultAnesth"));

if ($chir_id = dPgetParam( $_POST, 'chir_id'))
  mbSetValueToSession('chir_id', $chir_id);

$do = new CDoObjectAddEdit("CConsultation", "consultation_id");
$do->createMsg = "Consultation cr��e";
$do->modifyMsg = "Consultation modifi�e";
$do->deleteMsg = "Consultation supprim�e";
$do->doBind();
if (intval(dPgetParam($_POST, 'del'))) {
  $do->doDelete();
  $curr_consult = mbGetValueFromGetOrSession("consult_id", null);
  if($curr_consult == $do->_obj->consultation_id)
    mbSetValueToSession("consult_id");
} else {
  $do->doStore();
  if(@$_POST["_operation_id"]) {
    $consultAnesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '".$do->_obj->consultation_id."'";
    $where["operation_id"] = "= '".$_POST["_operation_id"]."'";
    $consultAnesth->loadObject($where);
    $consultAnesth->consultation_id = $do->_obj->consultation_id;
    $consultAnesth->operation_id = $_POST["_operation_id"];
    $consultAnesth->store();
  }
}

$do->doRedirect();

?>
