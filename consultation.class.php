<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('dp' ) );

require_once( $AppUI->getModuleClass('admin') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'files') );

// Enum for Consultation.chrono
define("CC_PLANIFIE"      , 16);
define("CC_PATIENT_ARRIVE", 32);
define("CC_EN_COURS"      , 48);
define("CC_TERMINE"       , 64);

class CConsultation extends CDpObject {
  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id = null;

  // DB fields
  var $heure = null;
  var $duree = null;
  var $secteur1 = null;
  var $secteur2 = null;
  var $chrono = null;
  var $annule = null;
  var $paye = null;
  var $cr_valide = null;
  var $motif = null;
  var $rques = null;
  var $examen = null;
  var $traitement = null;
  var $compte_rendu = null;
  var $premiere = null;
  var $tarif = null;
  var $type_tarif = null;

  // Form fields
  var $_etat = null;
  var $_hour = null;
  var $_min = null;
  var $_check_premiere = null; // CheckBox: must be present in all forms!

  // Object References
  var $_ref_patient = null;
  var $_ref_plageconsult = null;
  var $_ref_files = null;

  function CConsultation() {
    $this->CDpObject( 'consultation', 'consultation_id' );
  }
  
  function updateFormFields() {
    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));

    $etat = array();
    $etat[CC_PLANIFIE] = "Planifi�e";
    $etat[CC_PATIENT_ARRIVE] = "Patient arriv�";
    $etat[CC_EN_COURS] = "En cours";
    $etat[CC_TERMINE] = "Termin�e";
    
    $this->_etat = $etat[$this->chrono];
    if ($this->cr_valide) {
      $this->_etat = "CR Valid�";
    }

    if ($this->annule) {
      $this->_etat = "Annul�e";
    }
    
    $this->_check_premiere = $this->premiere;
  }
   
  function updateDBFields() {
    // To keep it null if no _hour nor _min
  	if ($this->_hour or $this->_min) {
      $this->heure = $this->_hour.":".$this->_min.":00";
    }
    
    $this->premiere = $this->_check_premiere ? 1 : 0;
  }
  
  function loadRefs() {
    // Forward references
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
    $this->_ref_plageconsult = new CPlageconsult;
    $this->_ref_plageconsult->load($this->plageconsult_id);
    
    // Backward references
    $where["file_consultation"] = "= '$this->consultation_id'";
    $this->_ref_files = new CFile();
    $this->_ref_files = $this->_ref_files->loadList($where);
  }
}

?>